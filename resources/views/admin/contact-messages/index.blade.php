@extends('layouts.admin')

@section('title', 'Contact Messages')

@section('admin_content')

<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Inquiries & Contact Messages</h1>
            <p class="text-xs text-gray-500">Messages sent via website contact form.</p>
        </div>
    </div>

    <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
        <table class="w-full text-left text-xs">
            <thead class="bg-gray-50 uppercase text-gray-500 border-b">
                <tr>
                    <th class="p-3">Sender Name</th>
                    <th class="p-3">Email</th>
                    <th class="p-3">Subject</th>
                    <th class="p-3">Date</th>
                    <th class="p-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y">
                @forelse($messages as $msg)
                    <tr class="hover:bg-gray-50 {{ $msg->is_read ? '' : 'font-bold bg-amber-50/50' }}">
                        <td class="p-3">{{ $msg->name }}</td>
                        <td class="p-3">{{ $msg->email }}</td>
                        <td class="p-3">{{ $msg->subject ?? 'Inquiry' }}</td>
                        <td class="p-3 text-gray-500">{{ $msg->created_at->format('M d, Y') }}</td>
                        <td class="p-3 text-right space-x-2">
                            <a href="{{ route('admin.contact-messages.show', $msg->id) }}" class="text-blue-600 hover:underline font-semibold">View</a>
                            <form action="{{ route('admin.contact-messages.destroy', $msg->id) }}" method="POST" class="inline" onsubmit="return confirm('Delete message?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:underline font-semibold">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-4 text-center text-gray-500">No contact messages received.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="p-4 border-t">
            {{ $messages->links() }}
        </div>
    </div>
</div>

@endsection
