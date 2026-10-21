<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ShopProduct;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class CheckoutController extends Controller
{
    public function index(CartService $cart)
    {
        $cart->syncWithDatabase();

        if ($cart->count() === 0) {
            return redirect()->route('shop.index')->with('error', 'Your shopping cart is currently empty. Add seeds or kits to checkout.');
        }

        $items = $cart->getItems();
        $subtotal = $cart->subtotal();
        $shippingCost = $cart->shippingCost();
        $total = $cart->total();
        $user = Auth::user();

        return view('shop.checkout', compact('items', 'subtotal', 'shippingCost', 'total', 'user'));
    }

    public function store(Request $request, CartService $cart)
    {
        $cart->syncWithDatabase();

        if ($cart->count() === 0) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        $rules = [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_state' => ['required', 'string', 'max:100'],
            'shipping_pincode' => ['required', 'string', 'max:20'],
            'payment_method' => ['required', 'in:cod,upi,bank_transfer'],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
        ];

        if (! Auth::check() && $request->boolean('create_account')) {
            $rules['password'] = ['required', 'confirmed', 'min:8'];
            $rules['customer_email'][] = 'unique:users,email';
        }

        $validated = $request->validate($rules);

        $order = DB::transaction(function () use ($validated, $request, $cart) {
            $cartItems = $cart->getItems();

            // Validate inventory with row lock to avoid race conditions
            foreach ($cartItems as $productId => $item) {
                $product = ShopProduct::where('id', $productId)->lockForUpdate()->first();

                if (! $product || ! $product->is_available || $product->stock_quantity < $item['quantity']) {
                    $available = $product ? $product->stock_quantity : 0;
                    throw ValidationException::withMessages([
                        'cart' => "Sorry, '{$item['name']}' only has {$available} units available in stock. Please adjust your cart.",
                    ]);
                }
            }

            // Create account if guest opted-in
            $userId = Auth::id();
            if (! $userId && $request->boolean('create_account')) {
                $newUser = User::create([
                    'name' => $validated['customer_name'],
                    'email' => $validated['customer_email'],
                    'password' => Hash::make($request->input('password')),
                    'role' => 'customer',
                    'phone' => $validated['customer_phone'],
                    'address' => $validated['shipping_address'],
                    'city' => $validated['shipping_city'],
                    'state' => $validated['shipping_state'],
                    'pincode' => $validated['shipping_pincode'],
                ]);
                Auth::login($newUser);
                $userId = $newUser->id;
            } elseif ($userId && empty(Auth::user()->address)) {
                // Save customer address if they didn't have one saved
                Auth::user()->update([
                    'phone' => $validated['customer_phone'],
                    'address' => $validated['shipping_address'],
                    'city' => $validated['shipping_city'],
                    'state' => $validated['shipping_state'],
                    'pincode' => $validated['shipping_pincode'],
                ]);
            }

            // Decrement inventory stock
            foreach ($cartItems as $productId => $item) {
                $product = ShopProduct::find($productId);
                $product->decrement('stock_quantity', $item['quantity']);
            }

            $order = Order::create([
                'order_number' => Order::generateOrderNumber(),
                'user_id' => $userId,
                'customer_name' => $validated['customer_name'],
                'customer_email' => $validated['customer_email'],
                'customer_phone' => $validated['customer_phone'],
                'shipping_address' => $validated['shipping_address'],
                'shipping_city' => $validated['shipping_city'],
                'shipping_state' => $validated['shipping_state'],
                'shipping_pincode' => $validated['shipping_pincode'],
                'subtotal' => $cart->subtotal(),
                'shipping_cost' => $cart->shippingCost(),
                'total_amount' => $cart->total(),
                'payment_method' => $validated['payment_method'],
                'payment_status' => 'pending',
                'status' => 'pending',
                'customer_notes' => $validated['customer_notes'] ?? null,
            ]);

            foreach ($cartItems as $productId => $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'shop_product_id' => $productId,
                    'product_name' => $item['name'],
                    'product_sku' => $item['sku'] ?? null,
                    'unit_price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'total_price' => round($item['price'] * $item['quantity'], 2),
                ]);
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'pending',
                'comment' => 'Order placed successfully by customer via ' . $order->paymentMethodLabel() . '.',
                'created_by' => 'Customer',
            ]);

            $cart->clear();

            return $order;
        });

        return redirect()->route('checkout.success', $order->order_number);
    }

    public function success(string $orderNumber)
    {
        $order = Order::with(['items.product', 'statusHistories'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('shop.success', compact('order'));
    }
}
