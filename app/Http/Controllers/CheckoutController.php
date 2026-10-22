<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ShopProduct;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\CartService;
use App\Services\RazorpayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
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
        $user = Auth::user();
        $state = old('shipping_state', $user->state ?? 'Punjab');
        $shippingCost = $cart->shippingCost($state);
        $total = $cart->total($state);
        $shippingBreakdown = $cart->shippingBreakdown($state);

        $enableSimpleUpi = SiteSetting::isSimpleUpiEnabled();
        $enableRazorpay = SiteSetting::isRazorpayEnabled();

        // If both disabled, fallback to simple UPI to avoid blocking checkout
        if (! $enableSimpleUpi && ! $enableRazorpay) {
            $enableSimpleUpi = true;
        }

        $defaultMethod = SiteSetting::get('default_payment_method');
        if (! $defaultMethod || ($defaultMethod === 'razorpay' && ! $enableRazorpay) || ($defaultMethod === 'upi' && ! $enableSimpleUpi)) {
            $defaultMethod = $enableRazorpay ? 'razorpay' : 'upi';
        }

        return view('shop.checkout', compact(
            'items',
            'subtotal',
            'shippingCost',
            'total',
            'shippingBreakdown',
            'user',
            'enableSimpleUpi',
            'enableRazorpay',
            'defaultMethod'
        ));
    }

    public function store(Request $request, CartService $cart, RazorpayService $razorpayService)
    {
        $cart->syncWithDatabase();

        if ($cart->count() === 0) {
            return redirect()->route('shop.index')->with('error', 'Your cart is empty.');
        }

        $allowedMethods = [];
        if (SiteSetting::isSimpleUpiEnabled()) {
            $allowedMethods[] = 'upi';
        }
        if (SiteSetting::isRazorpayEnabled()) {
            $allowedMethods[] = 'razorpay';
        }
        if (empty($allowedMethods)) {
            $allowedMethods = ['upi', 'razorpay'];
        }

        $rules = [
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_email' => ['required', 'email', 'max:255'],
            'customer_phone' => ['required', 'string', 'max:20'],
            'shipping_address' => ['required', 'string', 'max:500'],
            'shipping_city' => ['required', 'string', 'max:100'],
            'shipping_state' => ['required', 'string', 'max:100'],
            'shipping_pincode' => ['required', 'string', 'max:20'],
            'payment_method' => ['nullable', Rule::in($allowedMethods)],
            'customer_notes' => ['nullable', 'string', 'max:1000'],
        ];

        if (! Auth::check() && $request->boolean('create_account')) {
            $rules['password'] = ['required', 'confirmed', 'min:8'];
            $rules['customer_email'][] = 'unique:users,email';
        }

        $validated = $request->validate($rules);
        $selectedPaymentMethod = $validated['payment_method'] ?? $allowedMethods[0];

        $order = DB::transaction(function () use ($validated, $selectedPaymentMethod, $request, $cart) {
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
                'shipping_cost' => $cart->shippingCost($validated['shipping_state']),
                'total_amount' => $cart->total($validated['shipping_state']),
                'payment_method' => $selectedPaymentMethod,
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

        // If payment method is Razorpay, pre-create the Razorpay order
        if ($order->payment_method === 'razorpay') {
            $razorpayService->createOrder($order);
        }

        return redirect()->route('checkout.success', $order->order_number);
    }

    public function success(string $orderNumber)
    {
        $order = Order::with(['items.product', 'statusHistories'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('shop.success', compact('order'));
    }

    public function submitPayment(Request $request, string $orderNumber)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        if (Auth::check() && ! Auth::user()->isAdmin() && $order->user_id && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $validated = $request->validate([
            'transaction_id' => [
                'required',
                'string',
                'min:6',
                'max:100',
                Rule::unique('orders', 'transaction_id')->ignore($order->id),
            ],
            'payment_notes' => ['nullable', 'string', 'max:500'],
        ], [
            'transaction_id.required' => 'Please enter your UPI Transaction ID or UTR number.',
            'transaction_id.min' => 'The UPI Transaction ID / UTR must be at least 6 characters.',
            'transaction_id.unique' => 'This UPI Transaction ID / UTR has already been submitted for another order. Each payment transaction ID can only be used once.',
        ]);

        $txnId = trim($validated['transaction_id']);

        $order->update([
            'transaction_id' => $txnId,
            'payment_notes' => $validated['payment_notes'] ?? $order->payment_notes,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $order->status,
            'comment' => 'Customer submitted UPI Transaction ID / UTR: ' . $txnId . '. Payment verification in progress.',
            'created_by' => Auth::check() ? Auth::user()->name : ($order->customer_name . ' (Customer)'),
        ]);

        return back()->with('success', 'Transaction ID submitted successfully! Your order will be processed once our team verifies the payment.');
    }

    /**
     * Verify payment completed through the Razorpay Checkout modal.
     */
    public function verifyRazorpay(Request $request, string $orderNumber, RazorpayService $razorpayService)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        if (Auth::check() && ! Auth::user()->isAdmin() && $order->user_id && $order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access to this order.');
        }

        $validated = $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['nullable', 'string'],
        ]);

        $razorpayOrderId = trim($validated['razorpay_order_id']);
        $razorpayPaymentId = trim($validated['razorpay_payment_id']);
        $signature = $validated['razorpay_signature'] ? trim($validated['razorpay_signature']) : null;

        $isValid = $razorpayService->verifySignature($razorpayOrderId, $razorpayPaymentId, $signature);

        if (! $isValid) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment signature verification failed. Please check your credentials or try again.',
                ], 422);
            }

            return back()->with('error', 'Razorpay payment signature verification failed. Please try again.');
        }

        DB::transaction(function () use ($order, $razorpayOrderId, $razorpayPaymentId, $signature) {
            $order->update([
                'payment_status' => 'paid',
                'payment_verified_at' => now(),
                'status' => ($order->status === 'pending') ? 'processing' : $order->status,
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $signature,
                'transaction_id' => $razorpayPaymentId,
            ]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $order->status,
                'comment' => 'Online payment of ₹' . number_format((float) $order->total_amount, 2) . ' successfully captured via Razorpay. Payment ID: ' . $razorpayPaymentId . '.',
                'created_by' => 'Razorpay Gateway',
            ]);
        });

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Payment verified successfully! Your seed order is now processing.',
                'redirect_url' => route('checkout.success', $order->order_number),
            ]);
        }

        return redirect()->route('checkout.success', $order->order_number)
            ->with('success', 'Payment of ₹' . number_format((float) $order->total_amount, 2) . ' verified successfully via Razorpay!');
    }

    /**
     * Create or retrieve Razorpay order on demand.
     */
    public function createRazorpayOrder(string $orderNumber, RazorpayService $razorpayService)
    {
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        $result = $razorpayService->createOrder($order);

        return response()->json([
            'success' => true,
            'order_id' => $order->razorpay_order_id,
            'amount' => (int) round(((float) $order->total_amount) * 100),
            'currency' => 'INR',
            'key' => $razorpayService->getKeyId(),
        ]);
    }

    /**
     * Handle incoming webhooks from Razorpay.
     */
    public function razorpayWebhook(Request $request, RazorpayService $razorpayService)
    {
        $signature = $request->header('X-Razorpay-Signature');
        $payload = $request->getContent();

        if ($signature && $razorpayService->getWebhookSecret()) {
            if (! $razorpayService->verifyWebhookSignature($payload, $signature)) {
                return response()->json(['error' => 'Invalid webhook signature'], 400);
            }
        }

        $event = $request->input('event');
        $payment = $request->input('payload.payment.entity');

        if (in_array($event, ['payment.captured', 'order.paid']) && $payment) {
            $orderId = $payment['order_id'] ?? null;
            $orderNumber = $payment['notes']['order_number'] ?? null;
            $paymentId = $payment['id'] ?? null;

            $order = null;
            if ($orderId) {
                $order = Order::where('razorpay_order_id', $orderId)->first();
            }
            if (! $order && $orderNumber) {
                $order = Order::where('order_number', $orderNumber)->first();
            }

            if ($order && $order->payment_status !== 'paid') {
                DB::transaction(function () use ($order, $paymentId, $orderId, $event) {
                    $order->update([
                        'payment_status' => 'paid',
                        'payment_verified_at' => now(),
                        'status' => ($order->status === 'pending') ? 'processing' : $order->status,
                        'razorpay_payment_id' => $paymentId,
                        'transaction_id' => $paymentId,
                    ]);

                    OrderStatusHistory::create([
                        'order_id' => $order->id,
                        'status' => $order->status,
                        'comment' => 'Payment verified via Razorpay Webhook (' . $event . '). Payment ID: ' . $paymentId . '.',
                        'created_by' => 'Razorpay Webhook',
                    ]);
                });
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Provide real-time dynamic shipping quote based on destination state.
     */
    public function shippingQuote(Request $request, CartService $cart)
    {
        $state = $request->query('state', 'Punjab');
        $breakdown = $cart->shippingBreakdown($state);

        return response()->json([
            'success' => true,
            'subtotal' => $cart->subtotal(),
            'shippingCost' => $breakdown['shipping_cost'],
            'total' => $cart->total($state),
            'isFree' => $breakdown['is_free'],
            'isPunjab' => $breakdown['is_punjab'],
            'formattedWeight' => $breakdown['formatted_weight'],
            'totalGrams' => $breakdown['total_grams'],
            'totalKg' => $breakdown['total_kg'],
            'regionLabel' => $breakdown['region_label'],
            'freeShippingThreshold' => $breakdown['free_shipping_threshold'],
        ]);
    }
}
