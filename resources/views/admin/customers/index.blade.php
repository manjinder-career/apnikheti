@extends('layouts.admin')

@section('title', 'Customer Management')

@section('admin_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Customer Accounts</h1>
            <p class="mt-1 text-xs text-gray-500">Manage registered customers, view order histories, and contact information.</p>
        </div>
        <div class="bg-white border px-4 py-2 rounded-xl text-xs text-gray-600 shadow-sm flex items-center space-x-2">
            <span>👥</span>
            <span>Total Customers: <strong class="text-gray-900">{{ $totalCustomers }}</strong></span>
        </div>
    </div>

    <!-- Search Form -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <form action="{{ route('admin.customers.index') }}" method="GET" class="flex gap-3">
            <div class="flex-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search customer by name, email, phone, or city..."
                       class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
            </div>
            <button type="submit" class="bg-forest-700 hover:bg-forest-800 text-white font-semibold text-xs px-5 py-2.5 rounded-lg shadow">
                Search
            </button>
            @if(request('q'))
                <a href="{{ route('admin.customers.index') }}" class="text-xs text-gray-500 hover:underline self-center">Clear</a>
            @endif
        </form>
    </div>

    <!-- Customer Table -->
    <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 uppercase text-gray-500 border-b">
                    <tr>
                        <th class="p-3">Customer</th>
                        <th class="p-3">Phone</th>
                        <th class="p-3">Location</th>
                        <th class="p-3 text-center">Orders Placed</th>
                        <th class="p-3">Total Spend</th>
                        <th class="p-3">Registered</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($customers as $customer)
                        <tr class="hover:bg-gray-50">
                            <td class="p-3">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="font-bold text-gray-900 hover:text-emerald-700">
                                    {{ $customer->name }}
                                </a>
                                <span class="block text-[11px] text-gray-400">{{ $customer->email }}</span>
                            </td>
                            <td class="p-3 text-gray-600">{{ $customer->phone ?? '-' }}</td>
                            <td class="p-3 text-gray-600">
                                {{ $customer->city ? $customer->city . ', ' . $customer->state : ($customer->address ? 'Address Saved' : '-') }}
                            </td>
                            <td class="p-3 text-center">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold {{ $customer->orders_count > 0 ? 'bg-sky-100 text-sky-800' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $customer->orders_count }}
                                </span>
                            </td>
                            <td class="p-3 font-bold text-gray-900">
                                ₹{{ number_format((float) ($customer->orders_sum_total_amount ?? 0), 2) }}
                            </td>
                            <td class="p-3 text-gray-500 whitespace-nowrap">
                                {{ $customer->created_at->format('M d, Y') }}
                            </td>
                            <td class="p-3 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('admin.customers.show', $customer) }}" class="font-semibold text-emerald-700 hover:underline">
                                    View Profile
                                </a>
                                <a href="{{ route('admin.customers.edit', $customer) }}" class="font-semibold text-gray-600 hover:underline ml-2">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-8 text-center text-gray-500">
                                No registered customers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $customers->links() }}
</div>
@endsection
