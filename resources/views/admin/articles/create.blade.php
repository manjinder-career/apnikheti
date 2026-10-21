@extends('layouts.admin')

@section('title', 'Create Article')

@section('admin_content')

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Publish New Article / Guide</h1>
        <a href="{{ route('admin.articles.index') }}" class="text-xs font-semibold text-gray-600 hover:underline">&larr; Back to Articles</a>
    </div>

    <form action="{{ route('admin.articles.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded-xl border shadow-sm space-y-6">
        @csrf

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Article Category *</label>
                <select name="article_category_id" required class="w-full text-xs p-2.5 border rounded-lg">
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name_en }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Author Name</label>
                <input type="text" name="author_name" value="{{ old('author_name', 'Amrit Singh Chahal') }}" class="w-full text-xs p-2.5 border rounded-lg">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Title in Punjabi (ਸਿਰਲੇਖ) *</label>
                <input type="text" name="title_pa" required value="{{ old('title_pa') }}" placeholder="ਸਿਰਲੇਖ ਪੰਜਾਬੀ ਵਿੱਚ" class="w-full text-xs p-2.5 border rounded-lg font-gurmukhi">
            </div>
            <div>
                <label class="block text-xs font-semibold text-gray-700 mb-1">Title in English *</label>
                <input type="text" name="title_en" required value="{{ old('title_en') }}" placeholder="Title in English" class="w-full text-xs p-2.5 border rounded-lg">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Excerpt / Short Introduction</label>
            <textarea name="excerpt_en" rows="2" class="w-full text-xs p-2.5 border rounded-lg">{{ old('excerpt_en') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Full Content (English) *</label>
            <textarea name="content_en" rows="6" required class="w-full text-xs p-2.5 border rounded-lg">{{ old('content_en') }}</textarea>
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Full Content (Punjabi Optional)</label>
            <textarea name="content_pa" rows="4" class="w-full text-xs p-2.5 border rounded-lg font-gurmukhi">{{ old('content_pa') }}</textarea>
        </div>

        <div class="flex items-center space-x-6 text-xs font-semibold bg-gray-50 p-4 rounded-lg">
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_published" value="1" checked class="text-forest-700">
                <span>Publish Immediately</span>
            </label>
            <label class="flex items-center space-x-2">
                <input type="checkbox" name="is_featured" value="1" class="text-forest-700">
                <span>Featured Guide</span>
            </label>
        </div>

        <button type="submit" class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold py-3 rounded-lg text-xs shadow">
            Publish Article &rarr;
        </button>
    </form>
</div>

@endsection
