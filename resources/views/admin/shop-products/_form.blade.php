<div class="space-y-5">
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="sm:col-span-2">
            <label for="name" class="block text-xs font-semibold text-gray-700 mb-1">Product Name *</label>
            <input id="name" type="text" name="name" required maxlength="255" value="{{ old('name', $product->name) }}" class="w-full text-sm p-3 border rounded-lg">
            @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="sku" class="block text-xs font-semibold text-gray-700 mb-1">SKU (Item Code)</label>
            <input id="sku" type="text" name="sku" maxlength="50" value="{{ old('sku', $product->sku) }}" placeholder="e.g. BEJ-WHT-001" class="w-full text-sm p-3 border rounded-lg font-mono uppercase">
            @error('sku') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
        <div>
            <label for="product_type" class="block text-xs font-semibold text-gray-700 mb-1">Product Type *</label>
            <select id="product_type" name="product_type" required class="w-full text-sm p-3 border rounded-lg">
                <option value="seed" {{ old('product_type', $product->product_type) === 'seed' ? 'selected' : '' }}>Traditional seed</option>
                <option value="kit" {{ old('product_type', $product->product_type) === 'kit' ? 'selected' : '' }}>Seed kit</option>
            </select>
            @error('product_type') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="price" class="block text-xs font-semibold text-gray-700 mb-1">Price (₹ INR) *</label>
            <input id="price" type="number" name="price" required min="0" step="0.01" value="{{ old('price', $product->price) }}" class="w-full text-sm p-3 border rounded-lg">
            @error('price') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="stock_quantity" class="block text-xs font-semibold text-gray-700 mb-1">Stock Quantity *</label>
            <input id="stock_quantity" type="number" name="stock_quantity" required min="0" step="1" value="{{ old('stock_quantity', $product->stock_quantity ?? 50) }}" class="w-full text-sm p-3 border rounded-lg">
            @error('stock_quantity') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="unit" class="block text-xs font-semibold text-gray-700 mb-1">Packaging Unit *</label>
            <input id="unit" type="text" name="unit" required maxlength="50" value="{{ old('unit', $product->unit ?? 'packet') }}" placeholder="packet, kg, box" class="w-full text-sm p-3 border rounded-lg">
            @error('unit') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
            <label for="shop_product_category_id" class="block text-xs font-semibold text-gray-700 mb-1">Shop Category</label>
            <select id="shop_product_category_id" name="shop_product_category_id" class="w-full text-sm p-3 border rounded-lg">
                <option value="">No category</option>
                @foreach($categories as $category)
                    <option value="{{ $category->id }}" {{ (string) old('shop_product_category_id', $product->shop_product_category_id) === (string) $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                @endforeach
            </select>
            @error('shop_product_category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label for="weight" class="block text-xs font-semibold text-gray-700 mb-1">Packet Weight / Seed Count (Optional)</label>
            <input id="weight" type="text" name="weight" maxlength="50" value="{{ old('weight', $product->weight) }}" placeholder="e.g. 100g, 500g, 50 seeds" class="w-full text-sm p-3 border rounded-lg">
            @error('weight') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>
    </div>

    <div>
        <label for="description" class="block text-xs font-semibold text-gray-700 mb-1">Description</label>
        <textarea id="description" name="description" rows="4" class="w-full text-sm p-3 border rounded-lg" placeholder="Growing instructions, culinary uses, harvest information...">{{ old('description', $product->description) }}</textarea>
        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="image" class="block text-xs font-semibold text-gray-700 mb-1">Product Photo</label>
        <input id="image" type="file" name="image" accept="image/jpeg,image/png,image/webp" class="w-full text-sm p-2.5 border rounded-lg">
        <p class="mt-1 text-[11px] text-gray-500">JPG, PNG, or WebP. Max 4 MB. Uploaded photos appear on the shop page.</p>
        @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        @if($product->exists && $product->photoUrl())
            <img src="{{ $product->photoUrl() }}" alt="{{ $product->name }}" class="mt-3 h-28 w-40 rounded-lg object-cover border">
            <p class="mt-1 text-[11px] text-gray-500">Leave empty to keep the current photo.</p>
        @endif
    </div>

    <div>
        <label for="image_url" class="block text-xs font-semibold text-gray-700 mb-1">Or paste a photo URL (optional)</label>
        <input id="image_url" type="url" name="image_url" value="{{ old('image_url', $product->hasUploadedPhoto() ? '' : $product->image_url) }}" placeholder="https://example.com/product.jpg" class="w-full text-sm p-3 border rounded-lg">
        @error('image_url') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 rounded-lg bg-sky-50 p-4">
        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
            <input type="checkbox" name="is_available" value="1" {{ old('is_available', $product->exists ? $product->is_available : true) ? 'checked' : '' }}>
            Available to order
        </label>
        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $product->exists ? $product->is_published : true) ? 'checked' : '' }}>
            Publish on shop page
        </label>
    </div>

    <button type="submit" class="w-full rounded-lg bg-forest-700 px-4 py-3 text-sm font-bold text-white shadow hover:bg-forest-800">Save Product</button>
</div>
