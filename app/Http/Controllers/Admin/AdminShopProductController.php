<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopProductCategory;
use App\Models\ShopProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AdminShopProductController extends Controller
{
    public function index(Request $request)
    {
        $query = ShopProduct::with('category');

        if ($request->filled('q')) {
            $term = '%' . trim($request->string('q')) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('sku', 'like', $term);
            });
        }

        if ($request->filled('category_id')) {
            $query->where('shop_product_category_id', $request->input('category_id'));
        }

        if ($request->filled('stock_status')) {
            match ($request->string('stock_status')) {
                'out_of_stock' => $query->where('stock_quantity', '<=', 0),
                'low_stock' => $query->where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5),
                'in_stock' => $query->where('stock_quantity', '>', 5),
                default => null,
            };
        }

        $products = $query->latest()->paginate(15)->withQueryString();
        $categories = ShopProductCategory::orderBy('name')->get();
        $lowStockCount = ShopProduct::where('stock_quantity', '>', 0)->where('stock_quantity', '<=', 5)->count();
        $outOfStockCount = ShopProduct::where('stock_quantity', '<=', 0)->count();

        return view('admin.shop-products.index', compact('products', 'categories', 'lowStockCount', 'outOfStockCount'));
    }

    public function create()
    {
        return view('admin.shop-products.create', [
            'product' => new ShopProduct([
                'stock_quantity' => 50,
                'unit' => 'packet',
                'is_available' => true,
                'is_published' => true,
            ]),
            'categories' => ShopProductCategory::orderBy('name')->get(),
        ]);
    }

    public function store(Request $request)
    {
        ShopProduct::create($this->validatedData($request));

        return redirect()->route('admin.shop-products.index')->with('success', 'Shop product added.');
    }

    public function edit(ShopProduct $shop_product)
    {
        return view('admin.shop-products.edit', [
            'product' => $shop_product,
            'categories' => ShopProductCategory::orderBy('name')->get(),
        ]);
    }

    public function update(Request $request, ShopProduct $shop_product)
    {
        $data = $this->validatedData($request, $shop_product);
        $oldPhoto = $shop_product->image_url;
        $shop_product->update($data);

        if (($data['image_url'] ?? $oldPhoto) !== $oldPhoto) {
            $this->deleteUploadedPhoto($oldPhoto);
        }

        return redirect()->route('admin.shop-products.index')->with('success', 'Shop product updated.');
    }

    public function destroy(ShopProduct $shop_product)
    {
        $this->deleteUploadedPhoto($shop_product->image_url);
        $shop_product->delete();

        return redirect()->route('admin.shop-products.index')->with('success', 'Shop product deleted.');
    }

    private function validatedData(Request $request, ?ShopProduct $product = null): array
    {
        $imageUrl = $request->input('image_url');
        $isRemoteUrl = is_string($imageUrl) && str_starts_with($imageUrl, 'http');

        $validated = $request->validate([
            'sku' => [
                'nullable',
                'string',
                'max:50',
                Rule::unique('shop_products', 'sku')->ignore($product?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'product_type' => ['required', 'in:seed,kit'],
            'shop_product_category_id' => ['nullable', 'integer', 'exists:shop_product_categories,id'],
            'description' => ['nullable', 'string', 'max:5000'],
            'price' => ['required', 'numeric', 'min:0', 'max:99999999.99'],
            'stock_quantity' => ['nullable', 'integer', 'min:0', 'max:1000000'],
            'unit' => ['nullable', 'string', 'max:50'],
            'weight' => ['nullable', 'string', 'max:50'],
            'weight_grams' => ['nullable', 'integer', 'min:1', 'max:100000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:4096'],
            'image_url' => [
                'nullable',
                'string',
                'max:2048',
                Rule::when($isRemoteUrl, ['url']),
            ],
        ]);

        unset($validated['image']);

        $validated['stock_quantity'] = (int) ($validated['stock_quantity'] ?? ($product ? $product->stock_quantity : 50));
        $validated['unit'] = $validated['unit'] ?? ($product ? $product->unit : 'packet');

        if (! empty($validated['weight_grams'])) {
            $validated['weight_grams'] = (int) $validated['weight_grams'];
            if (empty($validated['weight'])) {
                $g = $validated['weight_grams'];
                $validated['weight'] = ($g >= 1000) ? (($g / 1000) . ' Kg') : ($g . 'g');
            }
        } elseif (! empty($validated['weight'])) {
            $str = strtolower(trim($validated['weight']));
            if (preg_match('/([\d\.]+)\s*(kg|kilo|kilogram)/', $str, $matches)) {
                $validated['weight_grams'] = (int) round(((float) $matches[1]) * 1000);
            } elseif (preg_match('/([\d\.]+)\s*(g|gm|gram|grams)/', $str, $matches)) {
                $validated['weight_grams'] = (int) round((float) $matches[1]);
            }
        }

        if ($request->hasFile('image')) {
            $validated['image_url'] = 'storage/'.$request->file('image')->store('shop-products', 'public');
        } elseif ($product && ! $request->filled('image_url')) {
            $validated['image_url'] = $product->image_url;
        } elseif (! $request->filled('image_url')) {
            $validated['image_url'] = null;
        }

        $validated['is_available'] = $request->boolean('is_available');
        $validated['is_published'] = $request->boolean('is_published');

        return $validated;
    }

    private function deleteUploadedPhoto(?string $path): void
    {
        if (is_string($path) && str_starts_with($path, 'storage/shop-products/')) {
            Storage::disk('public')->delete(substr($path, strlen('storage/')));
        }
    }
}
