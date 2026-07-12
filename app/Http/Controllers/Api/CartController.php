<?php

namespace App\Http\Controllers\Api;

use App\Api\Services\CartService as CartService;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CartRequest;
use App\Models\Cart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function __construct(
        private readonly CartService $cartService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $cartItems = $this->cartService->getUserCart($request->user()->id);

        return response()->json([
            'status' => 'success',
            'data' => $cartItems,
            'totals' => $this->cartService->totals($cartItems),
        ]);
    }

    public function store(CartRequest $request): JsonResponse
    {
        $result = $this->cartService->addToCart(
            $request->user()->id,
            $request->validated()
        );

        return response()->json([
            'status' => 'success',
            'message' => $result['message'],
            'data' => $result['cart'],
        ], $result['status_code']);
    }

    public function update(CartRequest $request, Cart $cart): JsonResponse
    {
        if (! $this->cartService->isOwner($cart, $request->user()->id)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Unauthorized',
            ], 403);
        }

        $cartItem = $this->cartService->updateQuantity(
            $cart,
            $request->validated()['quantity']
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Cart quantity updated successfully',
            'data' => $cartItem,
        ]);
    }

    public function destroy(Request $request, Cart $cart): JsonResponse
    {
        if (! $this->cartService->isOwner($cart, $request->user()->id)) {
            return response()->json([
                'status' => 'fail',
                'message' => 'Unauthorized',
            ], 403);
        }

        $this->cartService->delete($cart);

        return response()->json([
            'status' => 'success',
            'message' => 'Cart item removed successfully',
        ]);
    }
}
