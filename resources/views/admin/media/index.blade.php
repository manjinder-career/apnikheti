@extends('layouts.admin')

@section('title', 'Manage Media & Videos')

@section('admin_content')

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Media & Video Library</h1>
            <p class="text-xs text-gray-500">Manage photos and videos shown in the public galleries.</p>
        </div>
        <a href="{{ route('admin.media.create') }}" class="bg-forest-700 hover:bg-forest-800 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow">
            + Add Media Item
        </a>
    </div>

    <div class="flex flex-wrap gap-2">
        <a href="{{ route('admin.media.index') }}" class="rounded-lg px-3 py-2 text-xs font-semibold {{ request('type') ? 'bg-white text-gray-600 border' : 'bg-forest-700 text-white' }}">All media</a>
        <a href="{{ route('admin.media.index', ['type' => 'photo']) }}" class="rounded-lg px-3 py-2 text-xs font-semibold {{ request('type') === 'photo' ? 'bg-forest-700 text-white' : 'bg-white text-gray-600 border' }}">Photo gallery</a>
        <a href="{{ route('admin.media.index', ['type' => 'video']) }}" class="rounded-lg px-3 py-2 text-xs font-semibold {{ request('type') === 'video' ? 'bg-forest-700 text-white' : 'bg-white text-gray-600 border' }}">Video gallery</a>
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 uppercase text-gray-500 border-b">
                <tr>
                    <th class="p-3">Title</th>
                    <th class="p-3">Media Type</th>
                    <th class="p-3">Category</th>
                    <th class="p-3">Visibility</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($mediaItems as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-gray-900">
                            {{ $item->title }}
                            @if($item->media_type === 'photo')
                                <img src="{{ str_starts_with($item->url_or_path, 'http') ? $item->url_or_path : asset($item->url_or_path) }}" alt="" class="mt-2 h-12 w-16 rounded object-cover">
                            @endif
                        </td>
                        <td class="p-3 capitalize">{{ $item->media_type }}</td>
                        <td class="p-3">{{ $item->category ?? 'N/A' }}</td>
                        <td class="p-3">
                            <span class="rounded-full px-2.5 py-1 {{ $item->is_published ? 'bg-emerald-100 text-emerald-800' : 'bg-gray-100 text-gray-600' }}">{{ $item->is_published ? 'Published' : 'Hidden' }}</span>
                        </td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.media.edit', $item) }}" class="mr-3 font-semibold text-sky-700 hover:underline">Edit</a>
                            <form action="{{ route('admin.media.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete media item?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-semibold">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">No media items added.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
