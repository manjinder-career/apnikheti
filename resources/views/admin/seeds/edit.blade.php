@extends('layouts.admin')

@section('title', 'Edit Seed Record')

@section('admin_content')

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Edit Seed: {{ $seed->name_en }}</h1>
        <a href="{{ route('admin.seeds.index') }}" class="text-xs font-semibold text-gray-600 hover:underline">&larr; Back to Seeds</a>
    </div>

    <form action="{{ route('admin.seeds.update', $seed->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl border shadow-sm space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Seed Category *</label>
                <select name="seed_category_id" required class="w-full text-xs p-2.5 border rounded-lg">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $seed->seed_category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name_en }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Publication Status</label>
                <select name="publication_status" class="w-full text-xs p-2.5 border rounded-lg">
                    <option value="published" {{ $seed->publication_status == 'published' ? 'selected' : '' }}>Published</option>
                    <option value="draft" {{ $seed->publication_status == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option value="archived" {{ $seed->publication_status == 'archived' ? 'selected' : '' }}>Archived</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Seed Name in Punjabi *</label>
                <input type="text" name="name_pa" required value="{{ old('name_pa', $seed->name_pa) }}" class="w-full text-xs p-2.5 border rounded-lg font-gurmukhi">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Seed Name in English *</label>
                <input type="text" name="name_en" required value="{{ old('name_en', $seed->name_en) }}" class="w-full text-xs p-2.5 border rounded-lg">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Local Names</label>
                <input type="text" name="local_names" value="{{ old('local_names', $seed->local_names) }}" class="w-full text-xs p-2.5 border rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Scientific Name</label>
                <input type="text" name="scientific_name" value="{{ old('scientific_name', $seed->scientific_name) }}" class="w-full text-xs p-2.5 border rounded-lg">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Description (English)</label>
            <textarea name="description_en" rows="3" class="w-full text-xs p-2.5 border rounded-lg">{{ old('description_en', $seed->description_en) }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Description (Punjabi)</label>
            <textarea name="description_pa" rows="3" class="w-full text-xs p-2.5 border rounded-lg font-gurmukhi">{{ old('description_pa', $seed->description_pa) }}</textarea>
        </div>

        <div class="flex items-center space-x-6 text-xs font-semibold bg-gray-50 p-4 rounded-lg">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_verified" value="1" {{ $seed->is_verified ? 'checked' : '' }} class="text-forest-700">
                <span>Is Verified Traditional Variety</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_featured" value="1" {{ $seed->is_featured ? 'checked' : '' }} class="text-forest-700">
                <span>Featured on Homepage</span>
            </label>
        </div>

        <button type="submit" class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold py-3 rounded-lg text-xs shadow">
            Update Seed Record
        </button>
    </form>
</div>

@endsection
