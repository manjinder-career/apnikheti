@extends('layouts.admin')

@section('title', $item->exists ? 'Edit Media Item' : 'Add Media Item')

@section('admin_content')

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex justify-between items-center border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">{{ $item->exists ? 'Edit Media Item' : 'Add Media / YouTube Video' }}</h1>
        <a href="{{ route('admin.media.index') }}" class="text-xs font-semibold text-gray-600 hover:underline">&larr; Back to Media</a>
    </div>

    <form action="{{ $item->exists ? route('admin.media.update', $item) : route('admin.media.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl border shadow-sm space-y-4">
        @csrf
        @if($item->exists)
            @method('PUT')
        @endif

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Title *</label>
            <input type="text" name="title" required value="{{ old('title', $item->title) }}" placeholder="Media Title" class="w-full text-xs p-2.5 border rounded-lg">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">YouTube URL or photo URL</label>
            <input type="text" name="url_or_path" value="{{ old('url_or_path', $item->url_or_path) }}" placeholder="https://www.youtube.com/watch?v=..." class="w-full text-xs p-2.5 border rounded-lg">
            <p class="mt-1 text-[11px] text-gray-500">For videos, use a YouTube link. For photos, enter a public image URL or upload an image below.</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Media Type</label>
                <select name="media_type" class="w-full text-xs p-2.5 border rounded-lg">
                    <option value="video" {{ old('media_type', $item->media_type ?: 'video') === 'video' ? 'selected' : '' }}>YouTube Video</option>
                    <option value="photo" {{ old('media_type', $item->media_type ?: 'video') === 'photo' ? 'selected' : '' }}>Photo Gallery</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Category</label>
                <input type="text" name="category" value="{{ old('category', $item->category) }}" placeholder="Interview / Demonstration" class="w-full text-xs p-2.5 border rounded-lg">
            </div>
        </div>

        <div>
            @if(!$item->exists)
                <label class="block text-xs font-semibold text-gray-700 mb-1">Upload photos (JPG, PNG, or WebP; max 5 MB each)</label>
                <input type="file" name="images[]" accept="image/jpeg,image/png,image/webp" multiple class="w-full text-xs p-2.5 border rounded-lg">
                <p class="mt-1 text-[11px] text-gray-500">All uploaded photos will use the title and category above.</p>
                @error('images') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                @foreach($errors->getMessages() as $field => $messages)
                    @if(str_starts_with($field, 'images.'))
                        @foreach($messages as $message)
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @endforeach
                    @endif
                @endforeach
            @else
                <label class="block text-xs font-semibold text-gray-700 mb-1">Upload photo (JPG, PNG, or WebP; max 5 MB)</label>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp" class="w-full text-xs p-2.5 border rounded-lg">
                @if($item->media_type === 'photo')
                    <p class="mt-1 text-[11px] text-gray-500">Leave empty to keep the current photo.</p>
                @endif
                @error('image') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
            @endif
            @error('url_or_path') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full text-xs p-2.5 border rounded-lg">{{ old('description', $item->description) }}</textarea>
        </div>

        <div class="flex items-center space-x-2 text-xs font-semibold bg-gray-50 p-3 rounded-lg">
            <input type="checkbox" name="is_published" value="1" {{ old('is_published', $item->exists ? $item->is_published : true) ? 'checked' : '' }} class="text-forest-700">
            <span>Published on public site</span>
        </div>

        <button type="submit" class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold py-2.5 rounded-lg text-xs shadow">
            {{ $item->exists ? 'Update Media Item' : 'Save Media Item' }}
        </button>
    </form>
</div>

@endsection
