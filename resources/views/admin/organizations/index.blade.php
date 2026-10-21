@extends('layouts.admin')

@section('title', 'Manage Organizations')

@section('admin_content')

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Collaborating Organizations</h1>
            <p class="text-xs text-gray-500">Manage verified organization and partner profiles.</p>
        </div>
        <a href="{{ route('admin.organizations.create') }}" class="bg-forest-700 hover:bg-forest-800 text-white text-xs font-semibold px-4 py-2 rounded-lg shadow">
            + Add Organization
        </a>
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 uppercase text-gray-500 border-b">
                <tr>
                    <th class="p-3">Organization Name</th>
                    <th class="p-3">Collaboration Type</th>
                    <th class="p-3">Website</th>
                    <th class="p-3">Status</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($organizations as $org)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3 font-bold text-gray-900">{{ $org->name }}</td>
                        <td class="p-3">{{ $org->collaboration_type ?? 'Collaborator' }}</td>
                        <td class="p-3 text-blue-600">{{ $org->website_url ?? 'N/A' }}</td>
                        <td class="p-3">
                            @if($org->is_published)
                                <span class="bg-emerald-100 text-emerald-800 px-2 py-0.5 rounded font-bold">Published</span>
                            @else
                                <span class="bg-stone-100 text-gray-600 px-2 py-0.5 rounded">Draft</span>
                            @endif
                        </td>
                        <td class="p-3 text-right space-x-2">
                            <a href="{{ route('admin.organizations.edit', $org->id) }}" class="text-blue-600 hover:underline font-semibold">Edit</a>
                            <form action="{{ route('admin.organizations.destroy', $org->id) }}" method="POST" class="inline" onsubmit="return confirm('Remove organization?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-semibold">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">No organizations added yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection
