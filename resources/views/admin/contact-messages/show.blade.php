@extends('layouts.admin')

@section('title', 'View Message')

@section('admin_content')

<div class="max-w-3xl mx-auto space-y-6">
    <div class="flex justify-between items-center border-b pb-4">
        <h1 class="text-2xl font-bold text-gray-800">Message from {{ $message->name }}</h1>
        <a href="{{ route('admin.contact-messages.index') }}" class="text-xs font-semibold text-gray-600 hover:underline">&larr; Back to Messages</a>
    </div>

    <div class="bg-white p-6 rounded-xl border shadow-sm space-y-4">
        <div class="grid grid-cols-2 gap-4 text-xs bg-gray-50 p-4 rounded-lg">
            <div>
                <span class="block text-gray-500 font-bold">Sender Email:</span>
                <span class="text-gray-900 font-semibold">{{ $message->email }}</span>
            </div>
            <div>
                <span class="block text-gray-500 font-bold">Phone Number:</span>
                <span class="text-gray-900 font-semibold">{{ $message->phone ?? 'Not provided' }}</span>
            </div>
            <div class="col-span-2">
                <span class="block text-gray-500 font-bold">Subject:</span>
                <span class="text-gray-900 font-semibold">{{ $message->subject ?? 'Inquiry' }}</span>
            </div>
        </div>

        <div>
            <h3 class="text-xs font-bold text-gray-500 uppercase mb-2">Message Body</h3>
            <div class="bg-stone-50 p-4 rounded-lg text-xs leading-relaxed text-gray-800 space-y-2">
                {!! nl2br(e($message->message)) !!}
            </div>
        </div>

        <div class="text-xs text-gray-400 border-t pt-3">
            Received on: {{ $message->created_at->format('F d, Y \a\t g:i A') }}
        </div>
    </div>
</div>

@endsection
