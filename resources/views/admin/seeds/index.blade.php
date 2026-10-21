@extends('layouts.admin')

@section('title', 'Manage Seed Records')

@section('admin_content')

<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Seed Library Management</h1>
            <p class="text-xs text-gray-500">Create, edit, archive, and import/export seed records.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.seeds.export') }}" class="bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-semibold px-3.5 py-2 rounded-lg shadow flex items-center space-x-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                <span>Export CSV</span>
            </a>

            <!-- CSV Import Modal Trigger -->
            <button onclick="document.getElementById('importModal').classList.remove('hidden')" class="bg-blue-700 hover:bg-blue-800 text-white text-xs font-semibold px-3.5 py-2 rounded-lg shadow flex items-center space-x-1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                <span>Import CSV</span>
            </button>

            <a href="{{ route('admin.seeds.create') }}" class="bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow">
                + Create Seed Record
            </a>
        </div>
    </div>

    <!-- Search / Filter bar -->
    <div class="bg-white p-4 rounded-xl border shadow-sm">
        <form action="{{ route('admin.seeds.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search seed by name..." class="flex-1 text-xs px-3 py-2 rounded-lg border border-gray-300">
            <select name="category_id" class="text-xs px-3 py-2 rounded-lg border border-gray-300">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name_en }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-gray-800 text-white text-xs px-4 py-2 rounded-lg font-semibold">Filter</button>
        </form>
    </div>

    <!-- Seeds Table -->
    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 uppercase text-gray-500 border-b">
                <tr>
                    <th class="p-3">Seed Name (PA / EN)</th>
                    <th class="p-3">Category</th>
                    <th class="p-3">Season</th>
                    <th class="p-3">Verified</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($seeds as $seed)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">
                            <span class="block font-bold text-sm font-gurmukhi text-forest-800">{{ $seed->name_pa }}</span>
                            <span class="block text-gray-500">{{ $seed->name_en }}</span>
                        </td>
                        <td class="p-3">{{ $seed->category ? $seed->category->name_en : 'N/A' }}</td>
                        <td class="p-3">{{ $seed->growing_season ?? 'N/A' }}</td>
                        <td class="p-3">
                            @if($seed->is_verified)
                                <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded font-bold">Yes</span>
                            @else
                                <span class="bg-gray-100 text-gray-600 px-2 py-0.5 rounded">No</span>
                            @endif
                        </td>
                        <td class="p-3">
                            <span class="capitalize px-2 py-0.5 rounded font-semibold bg-stone-100">{{ $seed->publication_status }}</span>
                        </td>
                        <td class="p-3 text-right space-x-2">
                            <a href="{{ route('admin.seeds.edit', $seed->id) }}" class="text-blue-600 hover:underline font-semibold">Edit</a>
                            <form action="{{ route('admin.seeds.destroy', $seed->id) }}" method="POST" class="inline" onsubmit="return confirm('Archive this seed record?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-semibold">Archive</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="p-4 text-center text-gray-500">No seed records found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t">
            {{ $seeds->links() }}
        </div>
    </div>
</div>

<!-- CSV Import Modal -->
<div id="importModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl p-6 max-w-md w-full space-y-4 shadow-2xl">
        <div class="flex justify-between items-center border-b pb-2">
            <h3 class="font-bold text-lg text-gray-800">Import Seeds from CSV</h3>
            <button onclick="document.getElementById('importModal').classList.add('hidden')" class="text-gray-500 font-bold text-lg">&times;</button>
        </div>

        <form action="{{ route('admin.seeds.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Upload CSV File</label>
                <input type="file" name="csv_file" required accept=".csv" class="w-full text-xs p-2 border rounded-lg">
                <p class="text-xs text-gray-500 mt-1">Columns: Name (PA), Name (EN), Category, Scientific Name, Local Names, Region, Season, Soil, Water</p>
            </div>
            <div class="flex justify-end space-x-2 pt-2">
                <button type="button" onclick="document.getElementById('importModal').classList.add('hidden')" class="px-4 py-2 text-xs font-semibold bg-gray-200 rounded-lg">Cancel</button>
                <button type="submit" class="px-4 py-2 text-xs font-semibold bg-blue-700 text-white rounded-lg">Upload & Import</button>
            </div>
        </form>
    </div>
</div>

@endsection
