@extends('layouts.admin')

@section('title', 'Manage Seed Submissions')

@section('admin_content')

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Seed Contribution Submissions</h1>
            <p class="text-xs text-gray-500">Review contributions submitted by farmers and seed savers before publishing.</p>
        </div>
    </div>

    <!-- Status Tabs -->
    <div class="flex space-x-2 border-b">
        <a href="?status=pending" class="px-4 py-2 text-xs font-bold border-b-2 {{ $status == 'pending' ? 'border-amber-600 text-amber-700' : 'border-transparent text-gray-500' }}">Pending Review</a>
        <a href="?status=approved" class="px-4 py-2 text-xs font-bold border-b-2 {{ $status == 'approved' ? 'border-emerald-600 text-emerald-700' : 'border-transparent text-gray-500' }}">Approved</a>
        <a href="?status=rejected" class="px-4 py-2 text-xs font-bold border-b-2 {{ $status == 'rejected' ? 'border-red-600 text-red-700' : 'border-transparent text-gray-500' }}">Rejected</a>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 uppercase text-gray-500 border-b">
                <tr>
                    <th class="p-3">Contributor</th>
                    <th class="p-3">Seed Name (PA)</th>
                    <th class="p-3">Category</th>
                    <th class="p-3">Date</th>
                    <th class="p-3 text-right">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($submissions as $sub)
                    <tr class="hover:bg-gray-50">
                        <td class="p-3">
                            <span class="block font-bold text-gray-800">{{ $sub->contributor_name }}</span>
                            <span class="text-gray-500">{{ $sub->village ? $sub->village.', ' : '' }}{{ $sub->district }}</span>
                        </td>
                        <td class="p-3 font-gurmukhi font-bold text-sm text-forest-800">{{ $sub->seed_name_pa }}</td>
                        <td class="p-3">{{ $sub->crop_category_name ?? 'N/A' }}</td>
                        <td class="p-3 text-gray-500">{{ $sub->created_at->format('M d, Y') }}</td>
                        <td class="p-3 text-right">
                            <a href="{{ route('admin.submissions.show', $sub->id) }}" class="bg-amber-600 text-white px-3 py-1 rounded font-semibold text-xs hover:bg-amber-700">Inspect & Review</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">No submissions found under "{{ $status }}".</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t">
            {{ $submissions->links() }}
        </div>
    </div>
</div>

@endsection
