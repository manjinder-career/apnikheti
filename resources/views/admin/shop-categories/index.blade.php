@extends('layouts.admin')

@section('title', 'Shop Categories')

@section('admin_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Shop Categories</h1>
            <p class="mt-1 text-xs text-gray-500">Organize shop products into customer-facing categories.</p>
        </div>
        <a href="{{ route('admin.shop-categories.create') }}" class="inline-flex justify-center bg-forest-700 hover:bg-forest-800 text-white text-xs font-semibold px-4 py-2.5 rounded-lg shadow">+ Add Category</a>
    </div>

    <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-sky-50 uppercase text-gray-500 border-b">
                    <tr>
                        <th class="p-3">Category</th>
                        <th class="p-3">Products</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($categories as $category)
                        <tr>
                            <td class="p-3">
                                <span class="font-semibold text-gray-900">{{ $category->name }}</span>
                                @if($category->description)<span class="mt-1 block text-gray-500">{{ $category->description }}</span>@endif
                            </td>
                            <td class="p-3">{{ $category->products_count }}</td>
                            <td class="p-3 text-right whitespace-nowrap">
                                <a href="{{ route('admin.shop-categories.edit', $category) }}" class="font-semibold text-sky-700 hover:underline">Edit</a>
                                <form action="{{ route('admin.shop-categories.destroy', $category) }}" method="POST" class="inline ml-3" onsubmit="return confirm('Delete this category? Its products will remain uncategorized.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="font-semibold text-red-600 hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="p-8 text-center text-gray-500">No categories yet. Add one to organize the shop.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    {{ $categories->links() }}
</div>
@endsection
