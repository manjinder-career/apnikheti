@extends('layouts.admin')

@section('title', 'Manage Farmer Stories')

@section('admin_content')

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Farmer Stories & Experiences</h1>
            <p class="text-xs text-gray-500">Approve or archive stories submitted by farmers.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 uppercase text-gray-500 border-b">
                <tr>
                    <th class="p-3">Farmer Name</th>
                    <th class="p-3">Location</th>
                    <th class="p-3">Crop Name</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($stories as $story)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-gray-900">{{ $story->farmer_name }}</td>
                        <td class="p-3">{{ $story->village ? $story->village.', ' : '' }}{{ $story->district }}</td>
                        <td class="p-3">{{ $story->crop_name ?? 'N/A' }}</td>
                        <td class="p-3">
                            <span class="capitalize px-2 py-0.5 rounded font-bold text-xs {{ $story->status == 'published' ? 'bg-emerald-100 text-emerald-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $story->status }}
                            </span>
                        </td>
                        <td class="p-3 text-right space-x-2">
                            @if($story->status !== 'published')
                                <form action="{{ route('admin.farmer-stories.status', $story->id) }}" method="POST" class="inline">
                                    @csrf
                                    <input type="hidden" name="status" value="published">
                                    <button type="submit" class="bg-emerald-600 text-white px-2.5 py-1 rounded text-xs font-semibold">Approve</button>
                                </form>
                            @endif
                            <form action="{{ route('admin.farmer-stories.destroy', $story->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete story?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-semibold">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">No farmer stories submitted yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
