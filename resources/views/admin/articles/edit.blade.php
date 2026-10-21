@extends('layouts.admin')

@section('title', 'Edit Article')

@section('admin_content')

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Edit Article: {{ $article->title_en }}</h1>
        <a href="{{ route('admin.articles.index') }}" class="text-xs font-semibold text-gray-600 hover:underline">&larr; Back to Articles</a>
    </div>

    <form action="{{ route('admin.articles.update', $article->id) }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl border shadow-sm space-y-6">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Article Category *</label>
                <select name="article_category_id" required class="w-full text-xs p-2.5 border rounded-lg">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $article->article_category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name_en }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Author Name</label>
                <input type="text" name="author_name" value="{{ old('author_name', $article->author_name) }}" class="w-full text-xs p-2.5 border rounded-lg">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Title in Punjabi *</label>
                <input type="text" name="title_pa" required value="{{ old('title_pa', $article->title_pa) }}" class="w-full text-xs p-2.5 border rounded-lg font-gurmukhi">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Title in English *</label>
                <input type="text" name="title_en" required value="{{ old('title_en', $article->title_en) }}" class="w-full text-xs p-2.5 border rounded-lg">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Excerpt</label>
            <textarea name="excerpt_en" rows="2" class="w-full text-xs p-2.5 border rounded-lg">{{ old('excerpt_en', $article->excerpt_en) }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Full Content (English) *</label>
            <textarea name="content_en" rows="6" required class="w-full text-xs p-2.5 border rounded-lg">{{ old('content_en', $article->content_en) }}</textarea>
        </div>

        <div class="flex items-center space-x-6 text-xs font-semibold bg-gray-50 p-4 rounded-lg">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_published" value="1" {{ $article->is_published ? 'checked' : '' }} class="text-forest-700">
                <span>Is Published</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_featured" value="1" {{ $article->is_featured ? 'checked' : '' }} class="text-forest-700">
                <span>Featured Guide</span>
            </label>
        </div>

        <button type="submit" class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold py-3 rounded-lg text-xs shadow">
            Update Article
        </button>
    </form>
</div>

@endsection
