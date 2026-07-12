<?php

namespace App\Api\Services;

use App\Models\Cart;
use Illuminate\Support\Collection;

class CartService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }


    public function getUserCart(int $userId): Collection
    {
        return Cart::with('product')
            ->where('user_id', $userId)
            ->get()
            ->map(fn(Cart $cart) => $this->formatCartItem($cart));
    }

    public function addToCart(int $userId, array $data): array
    {
        $cartItem = Cart::where('user_id', $userId)
            ->where('product_id', $data['product_id'])
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $data['quantity']);

            return [
                'message' => 'Product quantity updated in cart',
                'cart' => $this->formatCartItem($cartItem->fresh()->load('product')),
                'status_code' => 200,
            ];
        }

        $cartItem = Cart::create([
            'user_id' => $userId,
            'product_id' => $data['product_id'],
            'quantity' => $data['quantity'],
        ]);

        return [
            'message' => 'Product added to cart',
            'cart' => $this->formatCartItem($cartItem->load('product')),
            'status_code' => 201,
        ];
    }

    public function updateQuantity(Cart $cart, int $quantity): array
    {
        $cart->update(['quantity' => $quantity]);

        return $this->formatCartItem($cart->fresh()->load('product'));
    }

    public function delete(Cart $cart): void
    {
        $cart->delete();
    }

    public function totals(Collection $cartItems): array
    {
        return [
            'total_items' => $cartItems->count(),
            'total_quantity' => $cartItems->sum('quantity'),
            'total_price' => $cartItems->sum('sub_total'),
        ];
    }

    public function isOwner(Cart $cart, int $userId): bool
    {
        return $cart->user_id === $userId;
    }

    private function formatCartItem(Cart $cart): array
    {
        $product = $cart->product;
        $unitPrice = $product->offer_price ?: $product->price;

        return [
            'id' => $cart->id,
            'product' => $product,
            'quantity' => $cart->quantity,
            'unit_price' => $unitPrice,
            'sub_total' => $unitPrice * $cart->quantity,
        ];
    }
}
