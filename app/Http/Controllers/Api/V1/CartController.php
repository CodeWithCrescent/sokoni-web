<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Resources\V1\CartResource;
use App\Models\Cart;
use App\Models\MarketProduct;
use Illuminate\Http\Request;

class CartController extends ApiController
{
    /**
     * @OA\Get(
     *     path="/cart",
     *     tags={"Cart"},
     *     summary="Get user's cart",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="market_id", in="query", @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Cart details")
     * )
     */
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Cart::with(['market', 'items.marketProduct.product.unit'])
            ->where('user_id', $user->id);

        if ($request->filled('market_id')) {
            $query->where('market_id', $request->market_id);
        }

        $carts = $query->get();

        return $this->successResponse(CartResource::collection($carts));
    }

    /**
     * @OA\Post(
     *     path="/cart/add",
     *     tags={"Cart"},
     *     summary="Add item to cart",
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"market_product_id", "quantity"},
     *         @OA\Property(property="market_product_id", type="integer"),
     *         @OA\Property(property="quantity", type="number"),
     *         @OA\Property(property="notes", type="string")
     *     )),
     *     @OA\Response(response=200, description="Item added to cart")
     * )
     */
    public function addItem(Request $request)
    {
        $request->validate([
            'market_product_id' => 'required|exists:market_products,id',
            'quantity' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = $request->user();
        $marketProduct = MarketProduct::findOrFail($request->market_product_id);

        $cart = Cart::firstOrCreate([
            'user_id' => $user->id,
            'market_id' => $marketProduct->market_id,
        ]);

        $item = $cart->addItem(
            $request->market_product_id,
            $request->quantity,
            $request->notes
        );

        $cart->load(['market', 'items.marketProduct.product.unit']);

        return $this->successResponse(
            new CartResource($cart),
            'Item added to cart'
        );
    }

    /**
     * @OA\Put(
     *     path="/cart/items/{itemId}",
     *     tags={"Cart"},
     *     summary="Update cart item quantity",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="itemId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\RequestBody(required=true, @OA\JsonContent(
     *         required={"quantity"},
     *         @OA\Property(property="quantity", type="number")
     *     )),
     *     @OA\Response(response=200, description="Cart item updated")
     * )
     */
    public function updateItem(Request $request, int $itemId)
    {
        $request->validate([
            'quantity' => 'required|numeric|min:0.01',
        ]);

        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)
            ->whereHas('items', fn($q) => $q->where('id', $itemId))
            ->firstOrFail();

        $cart->updateItemQuantity($itemId, $request->quantity);
        $cart->load(['market', 'items.marketProduct.product.unit']);

        return $this->successResponse(
            new CartResource($cart),
            'Cart item updated'
        );
    }

    /**
     * @OA\Delete(
     *     path="/cart/items/{itemId}",
     *     tags={"Cart"},
     *     summary="Remove item from cart",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="itemId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Item removed from cart")
     * )
     */
    public function removeItem(Request $request, int $itemId)
    {
        $user = $request->user();
        $cart = Cart::where('user_id', $user->id)
            ->whereHas('items', fn($q) => $q->where('id', $itemId))
            ->firstOrFail();

        $cart->removeItem($itemId);

        if ($cart->items()->count() === 0) {
            $cart->delete();
            return $this->successResponse(null, 'Cart cleared');
        }

        $cart->load(['market', 'items.marketProduct.product.unit']);

        return $this->successResponse(
            new CartResource($cart),
            'Item removed from cart'
        );
    }

    /**
     * @OA\Delete(
     *     path="/cart/{cartId}",
     *     tags={"Cart"},
     *     summary="Clear entire cart",
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(name="cartId", in="path", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="Cart cleared")
     * )
     */
    public function clear(Request $request, int $cartId)
    {
        $cart = Cart::where('user_id', $request->user()->id)
            ->where('id', $cartId)
            ->firstOrFail();

        $cart->clear();
        $cart->delete();

        return $this->successResponse(null, 'Cart cleared');
    }
}
