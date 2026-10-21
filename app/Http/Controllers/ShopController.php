<?php

namespace App\Http\Controllers;

use App\Models\ShopProduct;
use App\Models\ShopProductCategory;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $categories = ShopProductCategory::withCount(['products' => function ($query) {
            $query->where('is_published', true);
        }])->orderBy('name')->get();

        $query = ShopProduct::with('category')->where('is_published', true);

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($categoryQuery) => $categoryQuery->where('slug', $request->string('category')));
        }

        if ($request->filled('type')) {
            $query->where('product_type', $request->string('type'));
        }

        if ($request->filled('q')) {
            $searchTerm = '%' . trim($request->string('q')) . '%';
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', $searchTerm)
                  ->orWhere('description', 'like', $searchTerm)
                  ->orWhere('sku', 'like', $searchTerm);
            });
        }

        if ($request->boolean('in_stock_only')) {
            $query->where('is_available', true)->where('stock_quantity', '>', 0);
        }

        $sort = $request->string('sort', 'latest');
        match ($sort) {
            'price_low' => $query->orderBy('price', 'asc'),
            'price_high' => $query->orderBy('price', 'desc'),
            'name' => $query->orderBy('name', 'asc'),
            default => $query->latest(),
        };

        $products = $query
            ->paginate(12)
            ->withQueryString();

        return view('shop.index', compact('products', 'categories'));
    }

    public function show(ShopProduct $product)
    {
        if (! $product->is_published) {
            abort(404);
        }

        $relatedProducts = ShopProduct::where('is_published', true)
            ->where('id', '!=', $product->id)
            ->when($product->shop_product_category_id, function ($q) use ($product) {
                $q->where('shop_product_category_id', $product->shop_product_category_id);
            })
            ->inRandomOrder()
            ->take(3)
            ->get();

        return view('shop.show', compact('product', 'relatedProducts'));
    }
}
