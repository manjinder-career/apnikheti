<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AdminArticleController;
use App\Http\Controllers\Admin\AdminContactMessageController;
use App\Http\Controllers\Admin\AdminCustomerController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminFarmerStoryController;
use App\Http\Controllers\Admin\AdminMediaController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminOrganizationController;
use App\Http\Controllers\Admin\AdminShopProductCategoryController;
use App\Http\Controllers\Admin\AdminShopProductController;
use App\Http\Controllers\Admin\AdminSeedController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminSubmissionController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CustomerAccountController;
use App\Http\Controllers\CustomerAuthController;
use App\Http\Controllers\FarmerStoryController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\OrderTrackingController;
use App\Http\Controllers\OrganicFarmingController;
use App\Http\Controllers\OrganizationController;
use App\Http\Controllers\SeedController;
use App\Http\Controllers\SeedSubmissionController;
use App\Http\Controllers\ShopController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');

// Traditional Seed Library
Route::get('/seeds', [SeedController::class, 'index'])->name('seeds.index');
Route::get('/seeds/{slug}', [SeedController::class, 'show'])->name('seeds.show');

// Seed Contribution
Route::get('/contribute-seed', [SeedSubmissionController::class, 'create'])->name('seeds.contribute');
Route::post('/contribute-seed', [SeedSubmissionController::class, 'store'])->name('seeds.contribute.store');

// Organic & Natural Farming
Route::get('/organic-farming', [OrganicFarmingController::class, 'index'])->name('organic-farming');

// Farmer Stories
Route::get('/farmer-stories', [FarmerStoryController::class, 'index'])->name('farmer-stories.index');
Route::get('/farmer-stories/share', [FarmerStoryController::class, 'create'])->name('farmer-stories.create');
Route::post('/farmer-stories/share', [FarmerStoryController::class, 'store'])->name('farmer-stories.store');

// Organizations & Partners
Route::get('/organizations', [OrganizationController::class, 'index'])->name('organizations.index');

// Articles & Educational Resources
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/articles/{slug}', [ArticleController::class, 'show'])->name('articles.show');

// Media & Videos
Route::get('/media', [MediaController::class, 'index'])->name('media.index');
Route::get('/gallery', [MediaController::class, 'gallery'])->name('gallery.index');
Route::get('/videos', [MediaController::class, 'videos'])->name('videos.index');

// Seeds and Seed Kits Shop (E-Commerce)
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/{product}', [ShopController::class, 'show'])->name('shop.show');

// Cart Routes
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');
Route::patch('/cart/{id}', [CartController::class, 'update'])->name('cart.update');
Route::delete('/cart/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::post('/cart/clear', [CartController::class, 'clear'])->name('cart.clear');

// Checkout Routes
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
Route::get('/checkout/shipping-quote', [CheckoutController::class, 'shippingQuote'])->name('checkout.shipping-quote');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
Route::get('/orders/{orderNumber}/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::post('/orders/{orderNumber}/payment', [CheckoutController::class, 'submitPayment'])->name('orders.payment.submit');
Route::post('/orders/{orderNumber}/razorpay/verify', [CheckoutController::class, 'verifyRazorpay'])->name('orders.razorpay.verify');
Route::post('/orders/{orderNumber}/razorpay/order', [CheckoutController::class, 'createRazorpayOrder'])->name('orders.razorpay.order');
Route::post('/webhooks/razorpay', [CheckoutController::class, 'razorpayWebhook'])->name('webhooks.razorpay');

// Order Tracking
Route::get('/track-order', [OrderTrackingController::class, 'showForm'])->name('orders.track');

// Contact Page & Newsletter
Route::get('/contact', [ContactController::class, 'show'])->name('contact');
Route::post('/contact', [ContactController::class, 'submitMessage'])->name('contact.submit');
Route::post('/newsletter/subscribe', [ContactController::class, 'subscribeNewsletter'])->name('newsletter.subscribe');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/register', [CustomerAuthController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [CustomerAuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Customer Account Routes (Authenticated)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->prefix('account')->name('account.')->group(function () {
    Route::get('/', [CustomerAccountController::class, 'dashboard'])->name('dashboard');
    Route::get('/orders', [CustomerAccountController::class, 'orders'])->name('orders.index');
    Route::get('/orders/{orderNumber}', [CustomerAccountController::class, 'showOrder'])->name('orders.show');
    Route::post('/orders/{orderNumber}/cancel', [CustomerAccountController::class, 'cancelOrder'])->name('orders.cancel');
    Route::get('/profile', [CustomerAccountController::class, 'profile'])->name('profile');
    Route::put('/profile', [CustomerAccountController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Admin Panel Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Seed Management & CSV Import/Export
    Route::get('/seeds/export', [AdminSeedController::class, 'exportCsv'])->name('seeds.export');
    Route::post('/seeds/import', [AdminSeedController::class, 'importCsv'])->name('seeds.import');
    Route::resource('seeds', AdminSeedController::class);

    // Submission Review Workflow
    Route::get('/submissions', [AdminSubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/submissions/{submission}', [AdminSubmissionController::class, 'show'])->name('submissions.show');
    Route::post('/submissions/{submission}/approve', [AdminSubmissionController::class, 'approve'])->name('submissions.approve');
    Route::post('/submissions/{submission}/reject', [AdminSubmissionController::class, 'reject'])->name('submissions.reject');

    // E-Commerce: Product Management
    Route::resource('shop-categories', AdminShopProductCategoryController::class)->except(['show']);
    Route::resource('shop-products', AdminShopProductController::class)->except(['show']);

    // E-Commerce: Order Management
    Route::get('/orders', [AdminOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [AdminOrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('orders.status');
    Route::patch('/orders/{order}/verify-payment', [AdminOrderController::class, 'verifyPayment'])->name('orders.verify-payment');
    Route::get('/orders/{order}/invoice', [AdminOrderController::class, 'invoice'])->name('orders.invoice');

    // E-Commerce: Customer Management
    Route::resource('customers', AdminCustomerController::class)->only(['index', 'show', 'edit', 'update']);

    // Content Management
    Route::resource('articles', AdminArticleController::class);
    Route::resource('organizations', AdminOrganizationController::class);
    Route::resource('media', AdminMediaController::class)->except(['show']);

    // Farmer Stories Management
    Route::get('/farmer-stories', [AdminFarmerStoryController::class, 'index'])->name('farmer-stories.index');
    Route::post('/farmer-stories/{story}/status', [AdminFarmerStoryController::class, 'updateStatus'])->name('farmer-stories.status');
    Route::delete('/farmer-stories/{story}', [AdminFarmerStoryController::class, 'destroy'])->name('farmer-stories.destroy');

    // Site Settings (Founder Bio, Slogans, Statistics, Editable Info)
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

    // Contact Messages
    Route::get('/contact-messages', [AdminContactMessageController::class, 'index'])->name('contact-messages.index');
    Route::get('/contact-messages/{message}', [AdminContactMessageController::class, 'show'])->name('contact-messages.show');
    Route::delete('/contact-messages/{message}', [AdminContactMessageController::class, 'destroy'])->name('contact-messages.destroy');
});
