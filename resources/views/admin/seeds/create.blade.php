@extends('layouts.admin')

@section('title', 'Create Seed Record')

@section('admin_content')

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Create New Seed Record</h1>
        <a href="{{ route('admin.seeds.index') }}" class="text-xs font-semibold text-gray-600 hover:underline">&larr; Back to Seeds</a>
    </div>

    <form action="{{ route('admin.seeds.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl border shadow-sm space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Seed Category *</label>
                <select name="seed_category_id" required class="w-full text-xs p-2.5 border rounded-lg">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name_en }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Publication Status</label>
                <select name="publication_status" class="w-full text-xs p-2.5 border rounded-lg">
                    <option value="published">Published</option>
                    <option value="draft">Draft</option>
                    <option value="archived">Archived</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Seed Name in Punjabi (ਨਾਮ ਪੰਜਾਬੀ ਵਿੱਚ) *</label>
                <input type="text" name="name_pa" required value="{{ old('name_pa') }}" placeholder="ਮਿਸਾਲ: ਦੇਸੀ ਬੰਸੀ ਕਣਕ" class="w-full text-xs p-2.5 border rounded-lg font-gurmukhi">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Seed Name in English *</label>
                <input type="text" name="name_en" required value="{{ old('name_en') }}" placeholder="e.g. Desi Bansi Wheat" class="w-full text-xs p-2.5 border rounded-lg">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Local / Alternative Names</label>
                <input type="text" name="local_names" value="{{ old('local_names') }}" placeholder="Comma separated" class="w-full text-xs p-2.5 border rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Scientific Name (If Known)</label>
                <input type="text" name="scientific_name" value="{{ old('scientific_name') }}" placeholder="e.g. Triticum durum" class="w-full text-xs p-2.5 border rounded-lg">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Description (English)</label>
            <textarea name="description_en" rows="3" class="w-full text-xs p-2.5 border rounded-lg">{{ old('description_en') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Description (Punjabi)</label>
            <textarea name="description_pa" rows="3" class="w-full text-xs p-2.5 border rounded-lg font-gurmukhi">{{ old('description_pa') }}</textarea>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Growing Season</label>
                <input type="text" name="growing_season" value="{{ old('growing_season') }}" placeholder="Rabi / Kharif" class="w-full text-xs p-2.5 border rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Region of Origin</label>
                <input type="text" name="region_of_origin" value="{{ old('region_of_origin') }}" placeholder="Punjab" class="w-full text-xs p-2.5 border rounded-lg">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Crop Duration</label>
                <input type="text" name="crop_duration" value="{{ old('crop_duration') }}" placeholder="135 - 145 days" class="w-full text-xs p-2.5 border rounded-lg">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Traditional / Culinary Uses</label>
                <textarea name="traditional_uses" rows="2" class="w-full text-xs p-2.5 border rounded-lg">{{ old('traditional_uses') }}</textarea>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Seed Preservation Methods</label>
                <textarea name="preservation_methods" rows="2" class="w-full text-xs p-2.5 border rounded-lg">{{ old('preservation_methods') }}</textarea>
            </div>
        </div>

        <div class="flex items-center space-x-6 text-xs font-semibold bg-gray-50 p-4 rounded-lg">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_verified" value="1" checked class="text-forest-700">
                <span>Is Verified Traditional Variety</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_featured" value="1" class="text-forest-700">
                <span>Featured on Homepage</span>
            </label>
        </div>

        <button type="submit" class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold py-3 rounded-lg text-xs shadow">
            Save & Publish Seed Record
        </button>
    </form>
</div>

@endsection
