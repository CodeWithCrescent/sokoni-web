<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Requests\Api\V1\InitiatePaymentRequest;
use App\Http\Resources\V1\PaymentResource;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends ApiController
{
    /**
     * @OA\Post(
     *     path="/payments/initiate",
     *     tags={"Payments"},
     *     summary="Initiate a payment",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"order_id", "payment_method"},
     *         @OA\Property(property="order_id", type="integer"),
     *         @OA\Property(property="payment_method", type="string", enum={"mpesa", "card", "cash"}),
     *         @OA\Property(property="phone_number", type="string")
     *     )),
     *     @OA\Response(response=201, description="Payment initiated")
     * )
     */
    public function initiate(InitiatePaymentRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();

        $order = Order::findOrFail($data['order_id']);

        // Verify user owns the order
        if ($order->user_id !== $user->id && !$user->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        // Check if order is already paid
        if ($order->isPaid()) {
            return $this->errorResponse('Order is already paid', 422);
        }

        // Check for pending payment
        $pendingPayment = $order->payments()->pending()->first();
        if ($pendingPayment) {
            return $this->successResponse(
                new PaymentResource($pendingPayment),
                'Pending payment found'
            );
        }

        $payment = Payment::create([
            'order_id' => $order->id,
            'user_id' => $user->id,
            'payment_method' => $data['payment_method'],
            'amount' => $order->total,
            'currency' => 'TZS',
            'status' => Payment::STATUS_PENDING,
            'phone_number' => $data['phone_number'] ?? null,
            'external_reference' => 'AGZ' . time() . rand(1000, 9999),
        ]);

        // Here you would integrate with actual payment gateway
        // For M-Pesa: initiate STK push
        // For Card: return payment URL
        // For Cash: mark as pending cash payment

        if ($data['payment_method'] === Payment::METHOD_CASH) {
            $payment->status = Payment::STATUS_PROCESSING;
            $payment->save();
        }

        return $this->successResponse(
            new PaymentResource($payment),
            'Payment initiated successfully',
            201
        );
    }

    /**
     * @OA\Get(
     *     path="/payments/{id}",
     *     tags={"Payments"},
     *     summary="Get payment details",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Payment details")
     * )
     */
    public function show(Request $request, Payment $payment)
    {
        $user = $request->user();

        if ($payment->user_id !== $user->id && !$user->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        return $this->successResponse(new PaymentResource($payment));
    }

    /**
     * @OA\Post(
     *     path="/payments/{id}/confirm",
     *     tags={"Payments"},
     *     summary="Confirm a cash payment (admin only)",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Payment confirmed")
     * )
     */
    public function confirmCashPayment(Request $request, Payment $payment)
    {
        if ($payment->payment_method !== Payment::METHOD_CASH) {
            return $this->errorResponse('This payment is not a cash payment', 422);
        }

        if ($payment->isCompleted()) {
            return $this->errorResponse('Payment is already completed', 422);
        }

        $payment->markAsCompleted('CASH-' . time());

        // Update order status if needed
        $order = $payment->order;
        if ($order->isPending()) {
            $order->updateStatus(Order::STATUS_CONFIRMED, $request->user()->id, 'Payment confirmed');
        }

        return $this->successResponse(
            new PaymentResource($payment),
            'Cash payment confirmed successfully'
        );
    }

    /**
     * @OA\Post(
     *     path="/payments/callback",
     *     tags={"Payments"},
     *     summary="Payment gateway callback (webhook)",
     *     @OA\RequestBody(required=true, @OA\JsonContent()),
     *     @OA\Response(response=200, description="Callback processed")
     * )
     */
    public function callback(Request $request)
    {
        // This would handle M-Pesa/card payment callbacks
        // For now, just log and return success
        \Log::info('Payment callback received', $request->all());

        return response()->json(['status' => 'ok']);
    }

    /**
     * @OA\Get(
     *     path="/payments/order/{orderId}",
     *     tags={"Payments"},
     *     summary="Get payments for an order",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="orderId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="List of payments")
     * )
     */
    public function forOrder(Request $request, int $orderId)
    {
        $order = Order::findOrFail($orderId);
        $user = $request->user();

        if ($order->user_id !== $user->id && !$user->isAdmin()) {
            return $this->errorResponse('Unauthorized', 403);
        }

        $payments = $order->payments()->latest()->get();

        return $this->successResponse(PaymentResource::collection($payments));
    }
}
