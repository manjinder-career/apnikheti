@extends('layouts.admin')

@section('title', 'Manage Articles & Guides')

@section('admin_content')

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Articles & Educational Guides</h1>
            <p class="text-xs text-gray-500">Publish articles on soil health, seed conservation, and natural farming.</p>
        </div>
        <a href="{{ route('admin.articles.create') }}" class="bg-forest-700 hover:bg-forest-800 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow">
            + Publish New Article
        </a>
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 uppercase text-gray-500 border-b">
                <tr>
                    <th class="p-3">Title (EN / PA)</th>
                    <th class="p-3">Category</th>
                    <th class="p-3">Author</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($articles as $article)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">
                            <span class="block font-bold text-sm text-gray-900">{{ $article->title_en }}</span>
                            <span class="block font-gurmukhi text-emerald-800 font-semibold">{{ $article->title_pa }}</span>
                        </td>
                        <td class="p-3">{{ $article->category ? $article->category->name_en : 'N/A' }}</td>
                        <td class="p-3">{{ $article->author_name }}</td>
                        <td class="p-3">
                            @if($article->is_published)
                                <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded font-bold">Published</span>
                            @else
                                <span class="bg-stone-100 text-gray-600 px-2 py-0.5 rounded">Draft</span>
                            @endif
                        </td>
                        <td class="p-3 text-right space-x-2">
                            <a href="{{ route('admin.articles.edit', $article->id) }}" class="text-blue-600 hover:underline font-semibold">Edit</a>
                            <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete article?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-semibold">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">No articles created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t">
            {{ $articles->links() }}
        </div>
    </div>
</div>

@endsection
