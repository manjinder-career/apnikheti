@extends('layouts.admin')

@section('title', 'Review Seed Submission')

@section('admin_content')

<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex justify-between items-center border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Review Seed Contribution</h1>
        <a href="{{ route('admin.submissions.index') }}" class="text-xs font-semibold text-gray-600 hover:underline">&larr; Back to Submissions</a>
    </div>

    <!-- Submission Details Box -->
    <div class="bg-white p-6 rounded-xl border shadow-sm space-y-4">
        <div class="flex justify-between items-start border-b pb-3">
            <div>
                <span class="text-xs font-bold text-amber-600 uppercase">Contributor Details</span>
                <h2 class="text-lg font-bold text-gray-900">{{ $submission->contributor_name }}</h2>
                <p class="text-xs text-gray-500">{{ $submission->village ? $submission->village.', ' : '' }}{{ $submission->district }}, {{ $submission->state }}</p>
            </div>
            <span class="bg-amber-100 text-amber-900 px-3 py-1 rounded text-xs font-bold uppercase">{{ $submission->status }}</span>
        </div>

        <!-- Private Contact Details (Hidden from public) -->
        <div class="bg-stone-50 p-3 rounded-lg text-xs border space-y-1 text-gray-700">
            <p><strong>Private Contact Email:</strong> {{ $submission->email ?? 'Not provided' }}</p>
            <p><strong>Private Contact Phone:</strong> {{ $submission->phone ?? 'Not provided' }}</p>
            <p><strong>Contact Consent:</strong> {{ $submission->contact_consent ? 'Yes' : 'No' }}</p>
        </div>

        <div class="space-y-3 pt-2">
            <h3 class="text-xs font-bold text-gray-500 uppercase">Seed Data Submitted</h3>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs">
                <div class="p-3 bg-gray-50 rounded">
                    <span class="block font-bold text-gray-500">Seed Name (Punjabi)</span>
                    <span class="font-bold text-base font-gurmukhi text-forest-800">{{ $submission->seed_name_pa }}</span>
                </div>
                <div class="p-3 bg-gray-50 rounded">
                    <span class="block font-bold text-gray-500">Seed Name (English)</span>
                    <span class="font-bold text-sm text-gray-800">{{ $submission->seed_name_en ?? 'N/A' }}</span>
                </div>
            </div>

            <div>
                <span class="block text-xs font-bold text-gray-500">Description & Observations:</span>
                <p class="text-xs bg-gray-50 p-3 rounded mt-1 leading-relaxed text-gray-800">{{ $submission->description }}</p>
            </div>

            @if($submission->traditional_uses)
                <div>
                    <span class="block text-xs font-bold text-gray-500">Traditional Uses:</span>
                    <p class="text-xs bg-gray-50 p-3 rounded mt-1 text-gray-800">{{ $submission->traditional_uses }}</p>
                </div>
            @endif

            @if($submission->preservation_method)
                <div>
                    <span class="block text-xs font-bold text-gray-500">Preservation Method:</span>
                    <p class="text-xs bg-gray-50 p-3 rounded mt-1 text-gray-800">{{ $submission->preservation_method }}</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Review Action Form -->
    @if($submission->status == 'pending')
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <!-- Approve & Publish Form -->
            <div class="bg-emerald-50 border border-emerald-200 p-6 rounded-xl space-y-4">
                <h3 class="font-bold text-emerald-900 text-sm">Option A: Approve & Publish to Seed Library</h3>
                <form action="{{ route('admin.submissions.approve', $submission->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-emerald-900 mb-1">Assign Crop Category *</label>
                        <select name="seed_category_id" required class="w-full text-xs p-2.5 border rounded-lg">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name_en }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-emerald-900 mb-1">Internal Review Notes</label>
                        <input type="text" name="review_notes" value="Verified and approved." class="w-full text-xs p-2 border rounded-lg">
                    </div>
                    <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-2.5 rounded-lg text-xs shadow">
                        Approve & Create Published Seed &rarr;
                    </button>
                </form>
            </div>

            <!-- Reject Form -->
            <div class="bg-red-50 border border-red-200 p-6 rounded-xl space-y-4">
                <h3 class="font-bold text-red-900 text-sm">Option B: Reject Submission</h3>
                <form action="{{ route('admin.submissions.reject', $submission->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-red-900 mb-1">Reason for Rejection *</label>
                        <textarea name="review_notes" required rows="2" placeholder="e.g. Duplicate record / insufficient details..." class="w-full text-xs p-2 border rounded-lg"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-red-700 hover:bg-red-800 text-white font-bold py-2.5 rounded-lg text-xs shadow">
                        Reject Submission
                    </button>
                </form>
            </div>
        </div>
    @else
        <div class="bg-stone-100 p-4 rounded-xl text-xs text-gray-700">
            <strong>Reviewer Status:</strong> {{ ucfirst($submission->status) }}<br>
            <strong>Review Notes:</strong> {{ $submission->review_notes ?? 'None' }}
        </div>
    @endif
</div>

@endsection
