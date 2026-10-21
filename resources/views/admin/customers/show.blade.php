@extends('layouts.admin')

@section('title', 'Customer — ' . $customer->name)

@section('admin_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('admin.customers.index') }}" class="hover:underline">Customers</a>
                <span>&rsaquo;</span>
                <span class="font-bold text-gray-700">{{ $customer->name }}</span>
            </div>
            <h1 class="text-2xl font-bold text-gray-800">{{ $customer->name }}</h1>
            <p class="text-xs text-gray-500 mt-1">Customer Account • Registered {{ $customer->created_at->format('M d, Y') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.customers.edit', $customer) }}" class="bg-forest-700 hover:bg-forest-800 text-white font-semibold text-xs px-4 py-2 rounded-lg shadow">
                Edit Customer
            </a>
            <a href="{{ route('admin.customers.index') }}" class="text-xs text-gray-600 hover:underline px-3 py-2">
                &larr; Back to Customers
            </a>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
            <p class="text-xs text-gray-400 uppercase font-semibold">Total Orders</p>
            <p class="text-2xl font-bold text-gray-800 mt-1">{{ $customer->orders_count }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl border border-amber-200 shadow-sm">
            <p class="text-xs text-amber-700 uppercase font-semibold">Pending Deliveries</p>
            <p class="text-2xl font-bold text-amber-700 mt-1">{{ $pendingOrdersCount }}</p>
        </div>

        <div class="bg-white p-4 rounded-xl border border-emerald-200 shadow-sm">
            <p class="text-xs text-emerald-700 uppercase font-semibold">Lifetime Spend</p>
            <p class="text-2xl font-bold text-emerald-800 mt-1">₹{{ number_format((float) $totalSpent, 2) }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Customer Orders Table (2 cols) -->
        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white rounded-xl border shadow-sm overflow-hidden">
                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700">Order History</h2>
                    <span class="text-xs text-gray-500">{{ $orders->total() }} total</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50/50 uppercase text-gray-500 border-b text-[10px]">
                            <tr>
                                <th class="p-3">Order Number</th>
                                <th class="p-3">Date</th>
                                <th class="p-3">Items</th>
                                <th class="p-3">Total</th>
                                <th class="p-3">Status</th>
                                <th class="p-3 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-3 font-mono font-bold">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-emerald-700 hover:underline">
                                            {{ $order->order_number }}
                                        </a>
                                    </td>
                                    <td class="p-3 text-gray-500">{{ $order->created_at->format('M d, Y') }}</td>
                                    <td class="p-3 text-gray-700">{{ $order->items->count() }} items</td>
                                    <td class="p-3 font-bold text-gray-900">₹{{ number_format((float) $order->total_amount, 2) }}</td>
                                    <td class="p-3">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold border {{ $order->statusBadgeClasses() }}">
                                            {{ $order->statusLabel() }}
                                        </span>
                                    </td>
                                    <td class="p-3 text-right">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="text-emerald-700 hover:underline font-semibold">
                                            View &rarr;
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="p-6 text-center text-gray-400 italic">No orders placed by this customer yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{ $orders->links() }}
        </div>

        <!-- Customer Profile Card (1 col) -->
        <div class="space-y-6">
            <div class="bg-white rounded-xl border p-6 shadow-sm space-y-4 text-xs">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 border-b pb-3">
                    Contact & Address Info
                </h3>

                <div>
                    <span class="text-gray-400 block uppercase font-semibold text-[10px]">Email:</span>
                    <p class="font-medium text-gray-800">{{ $customer->email }}</p>
                </div>

                <div>
                    <span class="text-gray-400 block uppercase font-semibold text-[10px]">Phone:</span>
                    <p class="font-medium text-gray-800">{{ $customer->phone ?? 'Not provided' }}</p>
                </div>

                <div class="pt-2 border-t">
                    <span class="text-gray-400 block uppercase font-semibold text-[10px]">Default Shipping Address:</span>
                    @if($customer->address)
                        <p class="mt-1 leading-relaxed bg-gray-50 p-3 rounded border text-gray-700">
                            {{ $customer->address }}<br>
                            {{ $customer->city }}, {{ $customer->state }} — {{ $customer->pincode }}
                        </p>
                    @else
                        <p class="text-gray-400 italic mt-1">No default address saved yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
