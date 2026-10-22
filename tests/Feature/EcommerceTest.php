<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ShopProduct;
use App\Models\ShopProductCategory;
use App\Models\SiteSetting;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EcommerceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_customer_can_register_and_access_dashboard()
    {
        $response = $this->get(route('register'));
        $response->assertOk()->assertSee('Create Customer Account');

        $registerData = [
            'name' => 'Kuldeep Kaur',
            'email' => 'kuldeep@example.com',
            'phone' => '+91 98765 99999',
            'password' => 'secret123',
            'password_confirmation' => 'secret123',
            'address' => 'Farm #7, Green Fields',
            'city' => 'Jalandhar',
            'state' => 'Punjab',
            'pincode' => '144001',
        ];

        $postResponse = $this->post(route('register'), $registerData);
        $postResponse->assertRedirect(route('account.dashboard'));

        $this->assertAuthenticated();
        $user = User::where('email', 'kuldeep@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('customer', $user->role);
        $this->assertTrue($user->isCustomer());
        $this->assertEquals('+91 98765 99999', $user->phone);

        $dashResponse = $this->get(route('account.dashboard'));
        $dashResponse->assertOk()
            ->assertSee('Welcome back, Kuldeep Kaur')
            ->assertSee('Farm #7, Green Fields');
    }

    public function test_customer_login_redirects_to_account_dashboard()
    {
        $customer = User::where('email', 'customer@apnikheti.org')->first();

        $response = $this->post(route('login'), [
            'email' => $customer->email,
            'password' => 'password',
        ]);

        $response->assertRedirect(route('account.dashboard'));
        $this->assertAuthenticatedAs($customer);
    }

    public function test_customer_can_update_profile_and_shipping_address()
    {
        $customer = User::where('email', 'customer@apnikheti.org')->first();

        $this->actingAs($customer)
            ->get(route('account.profile'))
            ->assertOk()
            ->assertSee($customer->name);

        $updateResponse = $this->actingAs($customer)->put(route('account.profile.update'), [
            'name' => 'Gurpreet Singh Dhillon',
            'phone' => '+91 98888 77777',
            'address' => 'Updated Kothi #55, Sector 4',
            'city' => 'Mohali',
            'state' => 'Punjab',
            'pincode' => '160071',
        ]);

        $updateResponse->assertRedirect();
        $customer->refresh();
        $this->assertEquals('Gurpreet Singh Dhillon', $customer->name);
        $this->assertEquals('Updated Kothi #55, Sector 4', $customer->address);
        $this->assertEquals('160071', $customer->pincode);
    }

    public function test_shop_catalog_and_product_detail_page()
    {
        $product = ShopProduct::where('is_published', true)->first();

        $response = $this->get(route('shop.index'));
        $response->assertOk()
            ->assertSee($product->name)
            ->assertSee('Add to Cart');

        $detailResponse = $this->get(route('shop.show', $product));
        $detailResponse->assertOk()
            ->assertSee($product->name)
            ->assertSee('Add to Cart')
            ->assertSee('Buy Now');
    }

    public function test_shopping_cart_workflow()
    {
        $product = ShopProduct::where('is_published', true)->where('is_available', true)->where('stock_quantity', '>', 5)->first();

        // 1. Add item to cart
        $addResponse = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);
        $addResponse->assertRedirect();
        $addResponse->assertSessionHas('success');

        // 2. View Cart
        $cartView = $this->get(route('cart.index'));
        $cartView->assertOk()
            ->assertSee($product->name)
            ->assertSee('Cart Items (2)');

        // 3. Update quantity
        $updateResponse = $this->patch(route('cart.update', $product->id), [
            'quantity' => 3,
        ]);
        $updateResponse->assertRedirect();
        $this->get(route('cart.index'))->assertSee('Cart Items (3)');

        // 4. Stock enforcement - cannot exceed available stock
        $overstockResponse = $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => $product->stock_quantity + 50,
        ]);
        $overstockResponse->assertSessionHas('error');

        // 5. Remove item
        $removeResponse = $this->delete(route('cart.remove', $product->id));
        $removeResponse->assertRedirect();
        $this->get(route('cart.index'))->assertSee('Your basket is currently empty');
    }

    public function test_free_shipping_calculation()
    {
        $cart = app(CartService::class);
        $cart->clear();

        $cheapProduct = ShopProduct::create([
            'name' => 'Trial Seed Packet',
            'price' => 150.00,
            'stock_quantity' => 20,
            'is_available' => true,
            'is_published' => true,
        ]);

        $cart->add($cheapProduct, 1);
        $this->assertEquals(150.00, $cart->subtotal());
        $this->assertEquals(50.00, $cart->shippingCost());
        $this->assertEquals(200.00, $cart->total());

        // Above threshold (500)
        $cart->add($cheapProduct, 3); // 4 * 150 = 600
        $this->assertEquals(600.00, $cart->subtotal());
        $this->assertEquals(0.00, $cart->shippingCost());
        $this->assertEquals(600.00, $cart->total());

        $cart->clear();
    }

    public function test_checkout_and_order_placement_deducts_inventory()
    {
        $product = ShopProduct::where('is_published', true)->where('is_available', true)->where('stock_quantity', '>=', 5)->first();
        $initialStock = $product->stock_quantity;

        // Add to cart
        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 2,
        ]);

        $checkoutPage = $this->get(route('checkout.index'));
        $checkoutPage->assertOk()->assertSee('Delivery Details');

        $orderPayload = [
            'customer_name' => 'Amanjot Singh',
            'customer_email' => 'amanjot@example.com',
            'customer_phone' => '+91 98111 22222',
            'shipping_address' => 'Village Mansa Khurd',
            'shipping_city' => 'Mansa',
            'shipping_state' => 'Punjab',
            'shipping_pincode' => '151505',
            'payment_method' => 'upi',
            'customer_notes' => 'Call on arrival',
        ];

        $checkoutResponse = $this->post(route('checkout.store'), $orderPayload);

        $order = Order::where('customer_email', 'amanjot@example.com')->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('Amanjot Singh', $order->customer_name);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals('upi', $order->payment_method);
        $this->assertCount(1, $order->items);

        $checkoutResponse->assertRedirect(route('checkout.success', $order->order_number));

        // Check stock was decremented
        $product->refresh();
        $this->assertEquals($initialStock - 2, $product->stock_quantity);

        // Check status history was created
        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'status' => 'pending',
        ]);

        // Success page renders
        $this->get(route('checkout.success', $order->order_number))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Amanjot Singh');
    }

    public function test_order_tracking_lookup_and_customer_cancellation()
    {
        $customer = User::where('email', 'customer@apnikheti.org')->first();
        $order = Order::where('user_id', $customer->id)->where('status', 'processing')->first();
        $this->assertNotNull($order);

        // 1. Public tracking with order number and customer phone
        $trackResponse = $this->get(route('orders.track', [
            'order_number' => $order->order_number,
            'contact' => $customer->phone,
        ]));

        $trackResponse->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('Order Placed')
            ->assertSee('Packed')
            ->assertSee('Dispatched')
            ->assertDontSee('Out for Delivery');

        // 2. Customer cancel order from account dashboard
        $initialProductStock = $order->items->first()->product->stock_quantity;
        $orderedQuantity = $order->items->first()->quantity;

        $cancelResponse = $this->actingAs($customer)
            ->post(route('account.orders.cancel', $order->order_number), [
                'reason' => 'Changed my mind on variety.',
            ]);

        $cancelResponse->assertRedirect();
        $order->refresh();
        $this->assertEquals('cancelled', $order->status);

        // Verify stock was restored
        $order->items->first()->product->refresh();
        $this->assertEquals($initialProductStock + $orderedQuantity, $order->items->first()->product->stock_quantity);
    }

    public function test_admin_can_manage_products_inventory()
    {
        $admin = User::where('email', 'admin@apnikheti.org')->first();

        // 1. View inventory list
        $this->actingAs($admin)
            ->get(route('admin.shop-products.index'))
            ->assertOk()
            ->assertSee('Seed Products');

        // 2. Create product with SKU and stock
        $storeResponse = $this->actingAs($admin)->post(route('admin.shop-products.store'), [
            'name' => 'Rare Desi Barley (ਜੌਂ)',
            'sku' => 'BEJ-BRL-099',
            'product_type' => 'seed',
            'price' => 190.00,
            'stock_quantity' => 45,
            'unit' => 'packet',
            'weight' => '1 Kg',
            'description' => 'Traditional cold-tolerant indigenous barley variety.',
            'is_available' => true,
            'is_published' => true,
        ]);

        $storeResponse->assertRedirect(route('admin.shop-products.index'));

        $this->assertDatabaseHas('shop_products', [
            'sku' => 'BEJ-BRL-099',
            'name' => 'Rare Desi Barley (ਜੌਂ)',
            'stock_quantity' => 45,
        ]);

        $newProduct = ShopProduct::where('sku', 'BEJ-BRL-099')->first();

        // 3. Update product
        $this->actingAs($admin)->put(route('admin.shop-products.update', $newProduct), [
            'name' => 'Rare Desi Barley (Updated)',
            'sku' => 'BEJ-BRL-099',
            'product_type' => 'seed',
            'price' => 210.00,
            'stock_quantity' => 40,
            'unit' => 'packet',
            'is_available' => true,
            'is_published' => true,
        ])->assertRedirect(route('admin.shop-products.index'));

        $newProduct->refresh();
        $this->assertEquals(210.00, (float) $newProduct->price);
        $this->assertEquals(40, $newProduct->stock_quantity);
    }

    public function test_admin_can_manage_orders_and_update_dispatch_status()
    {
        $admin = User::where('email', 'admin@apnikheti.org')->first();
        $order = Order::first();

        // 1. Orders list
        $this->actingAs($admin)
            ->get(route('admin.orders.index'))
            ->assertOk()
            ->assertSee($order->order_number);

        // 2. Order detail
        $this->actingAs($admin)
            ->get(route('admin.orders.show', $order))
            ->assertOk()
            ->assertSee($order->customer_name);

        // 3. Update status to Shipped with tracking code
        $updateResponse = $this->actingAs($admin)->patch(route('admin.orders.status', $order), [
            'status' => 'shipped',
            'payment_status' => 'paid',
            'courier_name' => 'Blue Dart Express',
            'tracking_number' => 'BLUEDART12345',
            'status_comment' => 'Picked up by courier partner.',
            'admin_notes' => 'Packed with extra bubble wrap.',
        ]);

        $updateResponse->assertRedirect();
        $order->refresh();
        $this->assertEquals('shipped', $order->status);
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('BLUEDART12345', $order->tracking_number);

        // 4. View Invoice
        $this->actingAs($admin)
            ->get(route('admin.orders.invoice', $order))
            ->assertOk()
            ->assertSee($order->order_number)
            ->assertSee('BLUEDART12345');
    }

    public function test_admin_can_view_and_manage_customers()
    {
        $admin = User::where('email', 'admin@apnikheti.org')->first();
        $customer = User::where('email', 'customer@apnikheti.org')->first();

        // 1. Customer list
        $this->actingAs($admin)
            ->get(route('admin.customers.index'))
            ->assertOk()
            ->assertSee($customer->name)
            ->assertSee($customer->email);

        // 2. Customer show
        $this->actingAs($admin)
            ->get(route('admin.customers.show', $customer))
            ->assertOk()
            ->assertSee($customer->name)
            ->assertSee('Order History');

        // 3. Edit customer
        $this->actingAs($admin)
            ->put(route('admin.customers.update', $customer), [
                'name' => 'Gurpreet Singh AdminEdited',
                'email' => $customer->email,
                'phone' => '+91 99999 88888',
                'city' => 'Amritsar',
            ])
            ->assertRedirect(route('admin.customers.show', $customer));

        $customer->refresh();
        $this->assertEquals('Gurpreet Singh AdminEdited', $customer->name);
        $this->assertEquals('Amritsar', $customer->city);
    }

    public function test_order_page_displays_upi_id_scanner_and_verification_note()
    {
        $order = Order::where('order_number', 'AK-20261021-DEMO2')->first();
        $order->update(['payment_method' => 'upi', 'payment_status' => 'pending']);

        // Check public tracking page has UPI box, UPI ID, and note
        $response = $this->get(route('orders.track', [
            'order_number' => $order->order_number,
            'contact' => $order->customer_email,
        ]));

        $response->assertOk()
            ->assertSee('UPI Payment & Scanner', false)
            ->assertSee($order->getUpiId())
            ->assertSee('order will be processed after our team verifies the payment transaction ID', false);
    }

    public function test_user_can_submit_transaction_id_for_order()
    {
        $order = Order::where('order_number', 'AK-20261021-DEMO2')->first();

        $response = $this->post(route('orders.payment.submit', $order->order_number), [
            'transaction_id' => 'UPI429182749182',
        ]);

        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('UPI429182749182', $order->transaction_id);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'comment' => 'Customer submitted UPI Transaction ID / UTR: UPI429182749182. Payment verification in progress.',
        ]);
    }

    public function test_admin_can_verify_payment_and_mark_as_paid()
    {
        $admin = User::where('email', 'admin@apnikheti.org')->first();
        $order = Order::where('order_number', 'AK-20261021-DEMO2')->first();
        $order->update(['transaction_id' => 'TXN99887766', 'payment_status' => 'pending']);

        $response = $this->actingAs($admin)
            ->patch(route('admin.orders.verify-payment', $order));

        $response->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertNotNull($order->payment_verified_at);
        $this->assertEquals('processing', $order->status);
    }

    public function test_checkout_rejects_non_upi_payment_methods_and_requires_upi()
    {
        $product = ShopProduct::where('stock_quantity', '>', 5)->first();
        $this->post(route('cart.add'), ['product_id' => $product->id, 'quantity' => 1]);

        $invalidPayload = [
            'customer_name' => 'Harjit Singh',
            'customer_email' => 'harjit@example.com',
            'customer_phone' => '+91 98888 77777',
            'shipping_address' => 'VPO Bhikhi',
            'shipping_city' => 'Mansa',
            'shipping_state' => 'Punjab',
            'shipping_pincode' => '151504',
            'payment_method' => 'cod',
        ];

        $response = $this->post(route('checkout.store'), $invalidPayload);
        $response->assertSessionHasErrors('payment_method');
    }

    public function test_transaction_id_must_be_unique_across_orders_and_rejects_duplicates()
    {
        $order1 = Order::where('order_number', 'AK-20261021-DEMO1')->first();
        $order2 = Order::where('order_number', 'AK-20261021-DEMO2')->first();

        // 1. Submit transaction ID for order 1
        $this->post(route('orders.payment.submit', $order1->order_number), [
            'transaction_id' => 'UTR998877665544',
        ])->assertSessionHas('success');

        $order1->refresh();
        $this->assertEquals('UTR998877665544', $order1->transaction_id);

        // 2. Attempting to submit the exact same transaction ID on order 2 must be rejected
        $duplicateResponse = $this->post(route('orders.payment.submit', $order2->order_number), [
            'transaction_id' => 'UTR998877665544',
        ]);

        $duplicateResponse->assertSessionHasErrors(['transaction_id']);

        // 3. Re-submitting / updating on the SAME order (order 1) is allowed
        $this->post(route('orders.payment.submit', $order1->order_number), [
            'transaction_id' => 'UTR998877665544',
        ])->assertSessionHas('success');
    }

    public function test_admin_settings_can_toggle_payment_methods_and_configure_razorpay()
    {
        $admin = User::where('email', 'admin@apnikheti.org')->first();

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'payment_settings_submitted' => '1',
            'enable_simple_upi' => '1',
            'enable_razorpay' => '1',
            'default_payment_method' => 'razorpay',
            'razorpay_mode' => 'test',
            'razorpay_merchant_name' => 'Apni Kheti Test Gateway',
            'razorpay_key_id' => 'rzp_test_sampleKey123',
            'razorpay_key_secret' => 'sampleSecretValue456',
            'razorpay_webhook_secret' => 'sampleWebhookSecret789',
            'upi_id' => 'adminupi@oksbi',
            'upi_payee_name' => 'Apni Kheti Trust',
            'upi_note' => 'Custom order verification note.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('1', SiteSetting::get('enable_simple_upi'));
        $this->assertEquals('1', SiteSetting::get('enable_razorpay'));
        $this->assertEquals('razorpay', SiteSetting::get('default_payment_method'));
        $this->assertEquals('rzp_test_sampleKey123', SiteSetting::get('razorpay_key_id'));
        $this->assertEquals('sampleSecretValue456', SiteSetting::get('razorpay_key_secret'));
        $this->assertEquals('sampleWebhookSecret789', SiteSetting::get('razorpay_webhook_secret'));
        $this->assertEquals('adminupi@oksbi', SiteSetting::get('upi_id'));
    }

    public function test_admin_cannot_disable_both_payment_methods()
    {
        $admin = User::where('email', 'admin@apnikheti.org')->first();

        // Submitting with neither checkbox checked
        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'payment_settings_submitted' => '1',
            'upi_id' => 'adminupi@oksbi',
        ]);

        $response->assertSessionHasErrors('payment_methods');
    }

    public function test_checkout_with_razorpay_payment_method()
    {
        SiteSetting::set('enable_simple_upi', '1', 'ecommerce');
        SiteSetting::set('enable_razorpay', '1', 'ecommerce');

        $product = ShopProduct::where('is_published', true)->where('stock_quantity', '>=', 5)->first();

        // Add to cart
        $this->post(route('cart.add'), [
            'product_id' => $product->id,
            'quantity' => 1,
        ]);

        $checkoutResponse = $this->post(route('checkout.store'), [
            'customer_name' => 'Simranjit Kaur',
            'customer_email' => 'simranjit@example.com',
            'customer_phone' => '+91 98765 11111',
            'shipping_address' => 'House 12, Phase 3B2',
            'shipping_city' => 'Mohali',
            'shipping_state' => 'Punjab',
            'shipping_pincode' => '160059',
            'payment_method' => 'razorpay',
        ]);

        $order = Order::where('customer_email', 'simranjit@example.com')->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('razorpay', $order->payment_method);
        $this->assertEquals('pending', $order->payment_status);
        $this->assertNotNull($order->razorpay_order_id);

        $checkoutResponse->assertRedirect(route('checkout.success', $order->order_number));

        // Success page renders Razorpay box
        $this->get(route('checkout.success', $order->order_number))
            ->assertOk()
            ->assertSee('Razorpay Online Payment Gateway', false)
            ->assertSee('Pay')
            ->assertSee($order->order_number);
    }

    public function test_razorpay_payment_signature_verification_success_and_failure()
    {
        SiteSetting::set('razorpay_key_secret', 'rzp_secret_key_testing_123', 'ecommerce');

        $order = Order::where('order_number', 'AK-20261021-DEMO2')->first();
        $order->update([
            'payment_method' => 'razorpay',
            'payment_status' => 'pending',
            'status' => 'pending',
            'razorpay_order_id' => 'order_live_ABC123',
        ]);

        $orderId = 'order_live_ABC123';
        $paymentId = 'pay_live_XYZ789';
        $validSignature = hash_hmac('sha256', $orderId . '|' . $paymentId, 'rzp_secret_key_testing_123');

        // 1. Invalid signature attempt must fail
        $invalidResponse = $this->post(route('orders.razorpay.verify', $order->order_number), [
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => 'invalid_tampered_signature_string',
        ]);

        $invalidResponse->assertSessionHas('error');
        $order->refresh();
        $this->assertEquals('pending', $order->payment_status);

        // 2. Valid signature attempt must succeed
        $validResponse = $this->post(route('orders.razorpay.verify', $order->order_number), [
            'razorpay_order_id' => $orderId,
            'razorpay_payment_id' => $paymentId,
            'razorpay_signature' => $validSignature,
        ]);

        $validResponse->assertRedirect(route('checkout.success', $order->order_number));
        $validResponse->assertSessionHas('success');

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('processing', $order->status);
        $this->assertEquals($paymentId, $order->razorpay_payment_id);
        $this->assertEquals($paymentId, $order->transaction_id);
        $this->assertNotNull($order->payment_verified_at);

        $this->assertDatabaseHas('order_status_histories', [
            'order_id' => $order->id,
            'created_by' => 'Razorpay Gateway',
        ]);
    }

    public function test_razorpay_webhook_marks_order_as_paid()
    {
        SiteSetting::set('razorpay_webhook_secret', 'whsec_my_test_secret_999', 'ecommerce');

        $order = Order::where('order_number', 'AK-20261021-DEMO1')->first();
        $order->update([
            'payment_method' => 'razorpay',
            'payment_status' => 'pending',
            'status' => 'pending',
            'razorpay_order_id' => 'order_webhook_test_111',
        ]);

        $payloadData = [
            'event' => 'payment.captured',
            'payload' => [
                'payment' => [
                    'entity' => [
                        'id' => 'pay_webhook_99999',
                        'order_id' => 'order_webhook_test_111',
                        'amount' => 50000,
                        'currency' => 'INR',
                        'status' => 'captured',
                        'notes' => [
                            'order_number' => $order->order_number,
                        ],
                    ],
                ],
            ],
        ];

        $payloadJson = json_encode($payloadData);
        $signature = hash_hmac('sha256', $payloadJson, 'whsec_my_test_secret_999');

        $response = $this->call(
            'POST',
            route('webhooks.razorpay'),
            [],
            [],
            [],
            [
                'HTTP_X-Razorpay-Signature' => $signature,
                'CONTENT_TYPE' => 'application/json',
            ],
            $payloadJson
        );

        $response->assertOk();
        $response->assertJson(['status' => 'ok']);

        $order->refresh();
        $this->assertEquals('paid', $order->payment_status);
        $this->assertEquals('pay_webhook_99999', $order->razorpay_payment_id);
        $this->assertEquals('processing', $order->status);
    }

    public function test_checkout_respects_single_enabled_payment_method()
    {
        $product = ShopProduct::where('stock_quantity', '>=', 5)->first();

        // 1. Only Razorpay enabled: simple UPI rejected
        SiteSetting::set('enable_simple_upi', '0', 'ecommerce');
        SiteSetting::set('enable_razorpay', '1', 'ecommerce');

        $this->post(route('cart.add'), ['product_id' => $product->id, 'quantity' => 1]);

        $invalidUpi = $this->post(route('checkout.store'), [
            'customer_name' => 'Kuldip Singh',
            'customer_email' => 'kuldip@example.com',
            'customer_phone' => '+91 98888 12345',
            'shipping_address' => 'VPO Talwandi',
            'shipping_city' => 'Bathinda',
            'shipping_state' => 'Punjab',
            'shipping_pincode' => '151001',
            'payment_method' => 'upi',
        ]);
        $invalidUpi->assertSessionHasErrors('payment_method');

        // 2. Only Simple UPI enabled: razorpay rejected
        SiteSetting::set('enable_simple_upi', '1', 'ecommerce');
        SiteSetting::set('enable_razorpay', '0', 'ecommerce');

        $this->post(route('cart.add'), ['product_id' => $product->id, 'quantity' => 1]);

        $invalidRazorpay = $this->post(route('checkout.store'), [
            'customer_name' => 'Kuldip Singh',
            'customer_email' => 'kuldip@example.com',
            'customer_phone' => '+91 98888 12345',
            'shipping_address' => 'VPO Talwandi',
            'shipping_city' => 'Bathinda',
            'shipping_state' => 'Punjab',
            'shipping_pincode' => '151001',
            'payment_method' => 'razorpay',
        ]);
        $invalidRazorpay->assertSessionHasErrors('payment_method');

        // Reset both to enabled
        SiteSetting::set('enable_simple_upi', '1', 'ecommerce');
        SiteSetting::set('enable_razorpay', '1', 'ecommerce');
    }

    public function test_admin_can_set_product_weight_in_grams_and_display_label()
    {
        $admin = User::where('email', 'admin@apnikheti.org')->first();

        // 1. Create product with weight_grams
        $response = $this->actingAs($admin)->post(route('admin.shop-products.store'), [
            'name' => 'Heritage Wheat Seed Sack 5Kg',
            'sku' => 'WHT-5KG-001',
            'product_type' => 'seed',
            'price' => 450.00,
            'stock_quantity' => 20,
            'weight_grams' => 5000,
            'weight' => '5 Kg Sack',
            'is_available' => true,
            'is_published' => true,
        ]);

        $response->assertRedirect(route('admin.shop-products.index'));

        $product = ShopProduct::where('sku', 'WHT-5KG-001')->first();
        $this->assertNotNull($product);
        $this->assertEquals(5000, $product->weight_grams);
        $this->assertEquals(5000, $product->getWeightInGrams());
        $this->assertEquals('5 Kg Sack', $product->formattedWeight());

        // 2. Update product weight
        $updateResponse = $this->actingAs($admin)->put(route('admin.shop-products.update', $product), [
            'name' => 'Heritage Wheat Seed Sack 2Kg',
            'sku' => 'WHT-5KG-001',
            'product_type' => 'seed',
            'price' => 220.00,
            'stock_quantity' => 20,
            'weight_grams' => 2000,
            'weight' => '2 Kg Bag',
            'is_available' => true,
            'is_published' => true,
        ]);

        $updateResponse->assertRedirect(route('admin.shop-products.index'));
        $product->refresh();
        $this->assertEquals(2000, $product->weight_grams);
        $this->assertEquals('2 Kg Bag', $product->formattedWeight());
    }

    public function test_admin_settings_can_update_shipping_rates_for_punjab_and_outside()
    {
        $admin = User::where('email', 'admin@apnikheti.org')->first();

        $response = $this->actingAs($admin)->post(route('admin.settings.update'), [
            'payment_settings_submitted' => '1',
            'enable_simple_upi' => '1',
            'enable_razorpay' => '1',
            'shipping_punjab_base_cost' => '45.00',
            'shipping_punjab_per_kg' => '25.00',
            'shipping_punjab_free_threshold' => '600.00',
            'shipping_outside_base_cost' => '75.00',
            'shipping_outside_per_kg' => '45.00',
            'shipping_outside_free_threshold' => '900.00',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertEquals('45.00', SiteSetting::get('shipping_punjab_base_cost'));
        $this->assertEquals('25.00', SiteSetting::get('shipping_punjab_per_kg'));
        $this->assertEquals('600.00', SiteSetting::get('shipping_punjab_free_threshold'));
        $this->assertEquals('75.00', SiteSetting::get('shipping_outside_base_cost'));
        $this->assertEquals('45.00', SiteSetting::get('shipping_outside_per_kg'));
        $this->assertEquals('900.00', SiteSetting::get('shipping_outside_free_threshold'));
    }

    public function test_weight_based_shipping_calculation_for_punjab_and_outside_punjab()
    {
        // Configure standard rates for test
        SiteSetting::set('shipping_punjab_base_cost', '50.00', 'shipping');
        SiteSetting::set('shipping_punjab_per_kg', '30.00', 'shipping');
        SiteSetting::set('shipping_punjab_free_threshold', '500.00', 'shipping');

        SiteSetting::set('shipping_outside_base_cost', '80.00', 'shipping');
        SiteSetting::set('shipping_outside_per_kg', '50.00', 'shipping');
        SiteSetting::set('shipping_outside_free_threshold', '800.00', 'shipping');

        $cart = app(CartService::class);
        $cart->clear();

        // 1. Create lightweight product (200g each, ₹100 each)
        $seedPkt = ShopProduct::create([
            'name' => 'Desi Tomato Seeds (200g)',
            'price' => 100.00,
            'stock_quantity' => 50,
            'weight_grams' => 200,
            'is_available' => true,
            'is_published' => true,
        ]);

        // Add 2 packets (400g total, subtotal ₹200)
        $cart->add($seedPkt, 2);
        $this->assertEquals(400, $cart->totalWeightInGrams());
        $this->assertEquals(200.00, $cart->subtotal());

        // Under 1 Kg in Punjab: base rate ₹50
        $this->assertEquals(50.00, $cart->shippingCost('Punjab'));
        $this->assertEquals(250.00, $cart->total('Punjab'));

        // Under 1 Kg Outside Punjab (e.g. Haryana / Delhi): base rate ₹80
        $this->assertEquals(80.00, $cart->shippingCost('Delhi'));
        $this->assertEquals(280.00, $cart->total('Delhi'));

        // 2. Add heavy product (2.5 Kg = 2500g each, ₹120 each)
        $heavySack = ShopProduct::create([
            'name' => 'Desi Wheat Grain (2.5 Kg)',
            'price' => 120.00,
            'stock_quantity' => 50,
            'weight_grams' => 2500,
            'is_available' => true,
            'is_published' => true,
        ]);

        $cart->clear();
        $cart->add($heavySack, 1); // 2500g, subtotal ₹120
        $this->assertEquals(2500, $cart->totalWeightInGrams());

        // In Punjab: Base (1st 1kg) = ₹50 + ceil((2500-1000)/1000) * 30 = 50 + (2 * 30) = ₹110
        $this->assertEquals(110.00, $cart->shippingCost('Punjab'));
        $this->assertEquals(230.00, $cart->total('Punjab'));

        // Outside Punjab: Base (1st 1kg) = ₹80 + ceil((2500-1000)/1000) * 50 = 80 + (2 * 50) = ₹180
        $this->assertEquals(180.00, $cart->shippingCost('Haryana'));
        $this->assertEquals(300.00, $cart->total('Haryana'));

        // 3. Free shipping thresholds
        // In Punjab: above 500 => free shipping
        $cart->add($heavySack, 4); // 5 * 120 = ₹600 subtotal (>= 500 threshold)
        $this->assertEquals(600.00, $cart->subtotal());
        $this->assertEquals(0.00, $cart->shippingCost('Punjab'));

        // Outside Punjab threshold is 800, so at ₹600 it still pays shipping
        $this->assertGreaterThan(0.00, $cart->shippingCost('Rajasthan'));

        $cart->clear();
    }

    public function test_checkout_shipping_quote_endpoint()
    {
        $cart = app(CartService::class);
        $cart->clear();

        $product = ShopProduct::where('is_published', true)->where('stock_quantity', '>=', 5)->first();
        $cart->add($product, 1);

        // Quote for Punjab
        $punjabQuote = $this->getJson(route('checkout.shipping-quote', ['state' => 'Punjab']));
        $punjabQuote->assertOk();
        $punjabQuote->assertJson([
            'success' => true,
            'isPunjab' => true,
        ]);

        // Quote for Delhi
        $delhiQuote = $this->getJson(route('checkout.shipping-quote', ['state' => 'Delhi']));
        $delhiQuote->assertOk();
        $delhiQuote->assertJson([
            'success' => true,
            'isPunjab' => false,
        ]);

        $this->assertNotEquals($punjabQuote->json('shippingCost'), $delhiQuote->json('shippingCost'));

        $cart->clear();
    }

    public function test_checkout_applies_weight_and_state_based_shipping_to_order()
    {
        SiteSetting::set('shipping_punjab_base_cost', '50.00', 'shipping');
        SiteSetting::set('shipping_outside_base_cost', '80.00', 'shipping');

        $heavyItem = ShopProduct::create([
            'name' => 'Farm Bulk Mustard Seeds (3 Kg)',
            'price' => 200.00,
            'stock_quantity' => 15,
            'weight_grams' => 3000,
            'is_available' => true,
            'is_published' => true,
        ]);

        // 1. Order to outside Punjab (e.g. Rajasthan)
        $this->post(route('cart.add'), ['product_id' => $heavyItem->id, 'quantity' => 1]);

        $response = $this->post(route('checkout.store'), [
            'customer_name' => 'Jaipal Singh',
            'customer_email' => 'jaipal@example.com',
            'customer_phone' => '+91 98765 44444',
            'shipping_address' => 'Station Road',
            'shipping_city' => 'Jaipur',
            'shipping_state' => 'Rajasthan',
            'shipping_pincode' => '302001',
            'payment_method' => 'upi',
        ]);

        $order = Order::where('customer_email', 'jaipal@example.com')->first();
        $this->assertNotNull($order);
        $this->assertEquals('Rajasthan', $order->shipping_state);
        // Outside Punjab 3kg = 80 + (2 * 50) = 180
        $this->assertEquals(180.00, (float) $order->shipping_cost);
        $this->assertEquals(380.00, (float) $order->total_amount);
    }
}
