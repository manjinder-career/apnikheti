@extends('layouts.admin')

@section('title', 'Order Management')

@section('admin_content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Customer Orders</h1>
            <p class="mt-1 text-xs text-gray-500">Review seed orders, update dispatch statuses, add tracking details, and print packing slips.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.shop-products.index') }}" class="inline-flex items-center bg-white border border-gray-300 text-gray-700 text-xs font-semibold px-4 py-2.5 rounded-lg shadow-sm hover:bg-gray-50">
                🌱 Shop Inventory
            </a>
            <a href="{{ route('admin.customers.index') }}" class="inline-flex items-center bg-white border border-gray-300 text-gray-700 text-xs font-semibold px-4 py-2.5 rounded-lg shadow-sm hover:bg-gray-50">
                👥 Customers
            </a>
        </div>
    </div>

    <!-- Metrics Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <a href="{{ route('admin.orders.index') }}" class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm hover:border-gray-400 transition">
            <p class="text-[11px] font-bold uppercase tracking-wider text-gray-400">Total Orders</p>
            <p class="text-2xl font-black text-gray-800 mt-1">{{ $counts['total'] }}</p>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}" class="bg-amber-50/60 p-4 rounded-xl border border-amber-200 shadow-sm hover:border-amber-400 transition">
            <p class="text-[11px] font-bold uppercase tracking-wider text-amber-700">Pending</p>
            <p class="text-2xl font-black text-amber-800 mt-1">{{ $counts['pending'] }}</p>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'processing']) }}" class="bg-sky-50/60 p-4 rounded-xl border border-sky-200 shadow-sm hover:border-sky-400 transition">
            <p class="text-[11px] font-bold uppercase tracking-wider text-sky-700">Processing</p>
            <p class="text-2xl font-black text-sky-800 mt-1">{{ $counts['processing'] }}</p>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'shipped']) }}" class="bg-indigo-50/60 p-4 rounded-xl border border-indigo-200 shadow-sm hover:border-indigo-400 transition">
            <p class="text-[11px] font-bold uppercase tracking-wider text-indigo-700">Shipped</p>
            <p class="text-2xl font-black text-indigo-800 mt-1">{{ $counts['shipped'] }}</p>
        </a>

        <a href="{{ route('admin.orders.index', ['status' => 'delivered']) }}" class="bg-emerald-50/60 p-4 rounded-xl border border-emerald-200 shadow-sm hover:border-emerald-400 transition">
            <p class="text-[11px] font-bold uppercase tracking-wider text-emerald-700">Delivered</p>
            <p class="text-2xl font-black text-emerald-800 mt-1">{{ $counts['delivered'] }}</p>
        </a>
    </div>

    <!-- Filters & Search -->
    <div class="bg-white p-4 rounded-xl border border-gray-200 shadow-sm">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-56">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search by Order #, Customer Name, Email, Phone, or Tracking #..."
                       class="w-full text-xs px-3.5 py-2.5 rounded-lg border border-gray-300 focus:outline-none focus:border-forest-600">
            </div>

            <select name="status" class="text-xs px-3 py-2.5 rounded-lg border border-gray-300 bg-white">
                <option value="all">All Order Statuses</option>
                <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="processing" {{ request('status') === 'processing' ? 'selected' : '' }}>Processing</option>
                <option value="shipped" {{ request('status') === 'shipped' ? 'selected' : '' }}>Shipped</option>
                <option value="out_for_delivery" {{ request('status') === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                <option value="delivered" {{ request('status') === 'delivered' ? 'selected' : '' }}>Delivered</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>

            <select name="payment_status" class="text-xs px-3 py-2.5 rounded-lg border border-gray-300 bg-white">
                <option value="all">All Payment Statuses</option>
                <option value="pending" {{ request('payment_status') === 'pending' ? 'selected' : '' }}>Payment Pending</option>
                <option value="paid" {{ request('payment_status') === 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="refunded" {{ request('payment_status') === 'refunded' ? 'selected' : '' }}>Refunded</option>
                <option value="failed" {{ request('payment_status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>

            <button type="submit" class="bg-forest-700 hover:bg-forest-800 text-white font-semibold text-xs px-4 py-2.5 rounded-lg shadow">
                Filter Orders
            </button>

            @if(request()->hasAny(['q', 'status', 'payment_status']))
                <a href="{{ route('admin.orders.index') }}" class="text-xs text-gray-500 hover:underline self-center">Clear Filters</a>
            @endif
        </form>
    </div>

    <!-- Orders Table -->
    <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-gray-50 uppercase text-gray-500 border-b">
                    <tr>
                        <th class="p-3">Order Number</th>
                        <th class="p-3">Date</th>
                        <th class="p-3">Customer</th>
                        <th class="p-3">Total Items</th>
                        <th class="p-3">Total Amount</th>
                        <th class="p-3">Payment</th>
                        <th class="p-3">Order Status</th>
                        <th class="p-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($orders as $order)
                        <tr class="hover:bg-gray-50/80">
                            <td class="p-3 font-mono font-bold text-gray-900">
                                <a href="{{ route('admin.orders.show', $order) }}" class="text-emerald-700 hover:underline">
                                    {{ $order->order_number }}
                                </a>
                                @if($order->tracking_number)
                                    <span class="block text-[10px] text-gray-400 font-normal">🚚 {{ $order->tracking_number }}</span>
                                @endif
                            </td>
                            <td class="p-3 text-gray-500 whitespace-nowrap">
                                {{ $order->created_at->format('M d, Y') }}
                                <span class="block text-[10px] text-gray-400">{{ $order->created_at->format('h:i A') }}</span>
                            </td>
                            <td class="p-3">
                                <strong class="text-gray-900">{{ $order->customer_name }}</strong>
                                <span class="block text-gray-500 text-[11px]">{{ $order->customer_phone }}</span>
                                <span class="block text-gray-400 text-[10px]">{{ $order->shipping_city }}, {{ $order->shipping_state }}</span>
                            </td>
                            <td class="p-3 text-gray-700 font-medium">
                                {{ $order->items->count() }} {{ Str::plural('variety', $order->items->count()) }}
                            </td>
                            <td class="p-3 font-bold text-gray-900">
                                ₹{{ number_format((float) $order->total_amount, 2) }}
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold border {{ $order->paymentStatusBadgeClasses() }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                                <span class="block text-[10px] text-gray-400 mt-0.5">{{ $order->paymentMethodLabel() }}</span>
                            </td>
                            <td class="p-3 whitespace-nowrap">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold border {{ $order->statusBadgeClasses() }}">
                                    {{ $order->statusLabel() }}
                                </span>
                            </td>
                            <td class="p-3 text-right whitespace-nowrap space-x-2">
                                <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank" class="font-semibold text-gray-600 hover:text-gray-900 bg-gray-100 px-2 py-1 rounded">
                                    📄 Invoice
                                </a>
                                <a href="{{ route('admin.orders.show', $order) }}" class="font-bold text-white bg-forest-700 hover:bg-forest-800 px-2.5 py-1 rounded shadow-sm">
                                    Manage &rarr;
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="p-8 text-center text-gray-500">
                                No customer orders found matching your search.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{ $orders->links() }}
</div>
@endsection
