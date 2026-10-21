<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ContactMessage;
use App\Models\FarmerStory;
use App\Models\Order;
use App\Models\Organization;
use App\Models\Seed;
use App\Models\SeedSubmission;
use App\Models\ShopProduct;
use App\Models\User;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $publishedSeedsCount = Seed::published()->count();
        $pendingSubmissionsCount = SeedSubmission::where('status', 'pending')->count();
        $publishedArticlesCount = Article::published()->count();
        $pendingStoriesCount = FarmerStory::where('status', 'pending')->count();
        $publishedOrganizationsCount = Organization::published()->count();
        $unreadMessagesCount = ContactMessage::where('is_read', false)->count();

        // E-commerce metrics
        $totalOrdersCount = Order::count();
        $pendingOrdersCount = Order::whereIn('status', ['pending', 'processing'])->count();
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_amount');
        $lowStockCount = ShopProduct::where('stock_quantity', '<=', 5)->count();
        $totalCustomersCount = User::whereIn('role', ['customer', 'user'])->orWhereHas('orders')->count();
        $recentOrders = Order::with('items')->latest()->take(5)->get();

        $recentSubmissions = SeedSubmission::where('status', 'pending')->latest()->take(5)->get();
        $recentSeeds = Seed::with('category')->latest()->take(5)->get();

        return view('admin.dashboard', compact(
            'publishedSeedsCount',
            'pendingSubmissionsCount',
            'publishedArticlesCount',
            'pendingStoriesCount',
            'publishedOrganizationsCount',
            'unreadMessagesCount',
            'recentSubmissions',
            'recentSeeds',
            'totalOrdersCount',
            'pendingOrdersCount',
            'totalRevenue',
            'lowStockCount',
            'totalCustomersCount',
            'recentOrders'
        ));
    }
}
