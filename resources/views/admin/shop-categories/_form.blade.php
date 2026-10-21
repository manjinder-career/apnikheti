<div class="space-y-5">
    <div>
        <label for="name" class="block text-xs font-semibold text-gray-700 mb-1">Category name *</label>
        <input id="name" type="text" name="name" required maxlength="100" value="{{ old('name', $category->name) }}" class="w-full text-sm p-3 border rounded-lg">
        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <div>
        <label for="description" class="block text-xs font-semibold text-gray-700 mb-1">Description</label>
        <textarea id="description" name="description" rows="4" class="w-full text-sm p-3 border rounded-lg">{{ old('description', $category->description) }}</textarea>
        @error('description') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
    </div>
    <button type="submit" class="w-full rounded-lg bg-forest-700 px-4 py-3 text-sm font-bold text-white shadow hover:bg-forest-800">Save Category</button>
</div>
