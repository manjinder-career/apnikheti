<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderStatusHistory;
use App\Models\ShopProduct;
use App\Models\ShopProductCategory;
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
        $checkoutPage->assertOk()->assertSee('Customer & Delivery Details');

        $orderPayload = [
            'customer_name' => 'Amanjot Singh',
            'customer_email' => 'amanjot@example.com',
            'customer_phone' => '+91 98111 22222',
            'shipping_address' => 'Village Mansa Khurd',
            'shipping_city' => 'Mansa',
            'shipping_state' => 'Punjab',
            'shipping_pincode' => '151505',
            'payment_method' => 'cod',
            'customer_notes' => 'Call on arrival',
        ];

        $checkoutResponse = $this->post(route('checkout.store'), $orderPayload);

        $order = Order::where('customer_email', 'amanjot@example.com')->latest()->first();
        $this->assertNotNull($order);
        $this->assertEquals('Amanjot Singh', $order->customer_name);
        $this->assertEquals('pending', $order->status);
        $this->assertEquals('cod', $order->payment_method);
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
            ->assertSee('Packed');

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
}
