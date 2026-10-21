@extends('layouts.admin')

@section('title', 'Add Organization Profile')

@section('admin_content')

<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex justify-between items-center border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Add Organization Profile</h1>
        <a href="{{ route('admin.organizations.index') }}" class="text-xs font-semibold text-gray-600 hover:underline">&larr; Back to Organizations</a>
    </div>

    <form action="{{ route('admin.organizations.store') }}" method="POST" class="bg-white p-6 rounded-xl border shadow-sm space-y-4">
        @csrf

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Organization Name *</label>
            <input type="text" name="name" required value="{{ old('name') }}" placeholder="Organization Name" class="w-full text-xs p-2.5 border rounded-lg">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Website URL</label>
            <input type="url" name="website_url" value="{{ old('website_url') }}" placeholder="https://..." class="w-full text-xs p-2.5 border rounded-lg">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Type of Collaboration</label>
            <input type="text" name="collaboration_type" value="{{ old('collaboration_type') }}" placeholder="e.g. Seed Preservation & Workshops" class="w-full text-xs p-2.5 border rounded-lg">
        </div>

        <div>
            <label class="block text-xs font-semibold text-gray-700 mb-1">Description</label>
            <textarea name="description" rows="3" class="w-full text-xs p-2.5 border rounded-lg">{{ old('description') }}</textarea>
        </div>

        <div class="flex items-center space-x-2 text-xs font-semibold bg-gray-50 p-3 rounded-lg">
            <input type="checkbox" name="is_published" value="1" checked class="text-forest-700">
            <span>Publish Organization Profile</span>
        </div>

        <button type="submit" class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold py-2.5 rounded-lg text-xs shadow">
            Save Organization Profile
        </button>
    </form>
</div>

@endsection
