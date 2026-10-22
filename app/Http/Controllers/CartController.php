<?php

namespace App\Http\Controllers;

use App\Models\ShopProduct;
use App\Services\CartService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function index(CartService $cart)
    {
        $syncNotices = $cart->syncWithDatabase();
        $items = $cart->getItems();
        $subtotal = $cart->subtotal();
        $shippingCost = $cart->shippingCost();
        $total = $cart->total();
        $count = $cart->count();
        $freeShippingThreshold = $cart->freeShippingThreshold();
        $amountNeededForFreeShipping = $cart->amountNeededForFreeShipping();
        $formattedWeight = $cart->formattedTotalWeight();
        $breakdown = $cart->shippingBreakdown();

        return view('shop.cart', compact(
            'items',
            'subtotal',
            'shippingCost',
            'total',
            'count',
            'freeShippingThreshold',
            'amountNeededForFreeShipping',
            'formattedWeight',
            'breakdown',
            'syncNotices'
        ));
    }

    public function add(Request $request, CartService $cart)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:shop_products,id'],
            'quantity' => ['nullable', 'integer', 'min:1', 'max:10000'],
        ]);

        $product = ShopProduct::findOrFail($validated['product_id']);
        $quantity = (int) ($validated['quantity'] ?? 1);

        $result = $cart->add($product, $quantity);

        if ($request->wantsJson()) {
            return response()->json($result, $result['success'] ? 200 : 422);
        }

        if (! $result['success']) {
            return back()->with('error', $result['message']);
        }

        if ($request->boolean('buy_now')) {
            return redirect()->route('checkout.index');
        }

        return back()->with('success', $result['message']);
    }

    public function update(Request $request, int $id, CartService $cart)
    {
        $validated = $request->validate([
            'quantity' => ['required', 'integer', 'min:0', 'max:100'],
        ]);

        $result = $cart->update($id, (int) $validated['quantity']);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => $result['success'],
                'message' => $result['message'],
                'cartCount' => $cart->count(),
                'subtotal' => $cart->subtotal(),
                'shippingCost' => $cart->shippingCost(),
                'total' => $cart->total(),
            ]);
        }

        return back()->with($result['success'] ? 'success' : 'error', $result['message']);
    }

    public function remove(int $id, CartService $cart)
    {
        $result = $cart->remove($id);

        return back()->with('success', $result['message']);
    }

    public function clear(CartService $cart)
    {
        $cart->clear();

        return redirect()->route('shop.index')->with('success', 'Your cart has been cleared.');
    }
}
