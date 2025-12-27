<?php

namespace App\Http\Requests\Api\V1;

use App\Models\Payment;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InitiatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_id' => 'required|exists:orders,id',
            'payment_method' => [
                'required',
                Rule::in([Payment::METHOD_MPESA, Payment::METHOD_CARD, Payment::METHOD_CASH]),
            ],
            'phone_number' => 'required_if:payment_method,mpesa|nullable|string|max:20',
        ];
    }
}
