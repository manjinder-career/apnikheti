<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopProductCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class AdminShopProductCategoryController extends Controller
{
    public function index()
    {
        $categories = ShopProductCategory::withCount('products')->orderBy('name')->paginate(20);

        return view('admin.shop-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.shop-categories.create', ['category' => new ShopProductCategory()]);
    }

    public function store(Request $request)
    {
        ShopProductCategory::create($this->validatedData($request));

        return redirect()->route('admin.shop-categories.index')->with('success', 'Shop category created.');
    }

    public function edit(ShopProductCategory $shop_category)
    {
        return view('admin.shop-categories.edit', ['category' => $shop_category]);
    }

    public function update(Request $request, ShopProductCategory $shop_category)
    {
        $shop_category->update($this->validatedData($request, $shop_category));

        return redirect()->route('admin.shop-categories.index')->with('success', 'Shop category updated.');
    }

    public function destroy(ShopProductCategory $shop_category)
    {
        $shop_category->delete();

        return redirect()->route('admin.shop-categories.index')->with('success', 'Shop category deleted. Products in this category remain in the shop uncategorized.');
    }

    private function validatedData(Request $request, ?ShopProductCategory $category = null): array
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('shop_product_categories', 'name')->ignore($category)],
            'description' => ['nullable', 'string', 'max:2000'],
        ]);

        $slug = Str::slug($validated['name']);
        Validator::make(
            ['slug' => $slug],
            ['slug' => ['required', Rule::unique('shop_product_categories', 'slug')->ignore($category)]]
        )->validate();

        $validated['slug'] = $slug;

        return $validated;
    }
}
