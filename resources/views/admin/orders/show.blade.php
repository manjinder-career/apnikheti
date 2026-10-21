@extends('layouts.admin')

@section('title', 'Manage Order #' . $order->order_number)

@section('admin_content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b pb-4">
        <div>
            <div class="flex items-center space-x-2 text-xs text-gray-500 mb-1">
                <a href="{{ route('admin.orders.index') }}" class="hover:underline">Orders</a>
                <span>&rsaquo;</span>
                <span class="font-mono font-bold text-gray-700">{{ $order->order_number }}</span>
            </div>
            <div class="flex items-center space-x-3">
                <h1 class="text-2xl font-bold text-gray-800">Order #{{ $order->order_number }}</h1>
                <span class="px-3 py-1 rounded-full text-xs font-bold border {{ $order->statusBadgeClasses() }}">
                    {{ $order->statusLabel() }}
                </span>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold border {{ $order->paymentStatusBadgeClasses() }}">
                    {{ ucfirst($order->payment_status) }}
                </span>
            </div>
            <p class="text-xs text-gray-500 mt-1">Placed on {{ $order->created_at->format('M d, Y \a\t h:i A') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <a href="{{ route('admin.orders.invoice', $order) }}" target="_blank"
               class="inline-flex items-center space-x-1.5 bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 text-xs font-semibold px-4 py-2 rounded-lg shadow-sm">
                <span>🖨️ Print Invoice</span>
            </a>
            <a href="{{ route('admin.orders.index') }}" class="text-xs font-semibold text-gray-600 hover:underline px-3 py-2">
                &larr; Back to Orders
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left: Items & Timeline (2 cols) -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Line Items -->
            <div class="overflow-hidden rounded-xl border bg-white shadow-sm">
                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                    <h2 class="text-xs font-bold uppercase tracking-wider text-gray-700">Order Items ({{ $order->items->count() }})</h2>
                    <span class="text-xs text-gray-500 font-medium">All amounts in INR (₹)</span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-gray-50/50 text-gray-500 uppercase border-b text-[10px]">
                            <tr>
                                <th class="p-3">Item Description</th>
                                <th class="p-3">SKU</th>
                                <th class="p-3">Unit Price</th>
                                <th class="p-3">Quantity</th>
                                <th class="p-3 text-right">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($order->items as $item)
                                <tr class="hover:bg-gray-50">
                                    <td class="p-3 font-semibold text-gray-900">
                                        <div class="flex items-center space-x-3">
                                            @if($item->product && $item->product->photoUrl())
                                                <img src="{{ $item->product->photoUrl() }}" alt="" class="w-10 h-10 rounded object-cover border flex-shrink-0">
                                            @else
                                                <div class="w-10 h-10 rounded bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-base flex-shrink-0">🌱</div>
                                            @endif
                                            <div>
                                                <span>{{ $item->product_name }}</span>
                                                @if($item->product)
                                                    <a href="{{ route('admin.shop-products.edit', $item->product) }}" class="block text-[10px] text-emerald-700 hover:underline">
                                                        View Product Stock ({{ $item->product->stock_quantity }} in stock)
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-3 font-mono text-gray-500">{{ $item->product_sku ?? '-' }}</td>
                                    <td class="p-3 font-medium">₹{{ number_format((float) $item->unit_price, 2) }}</td>
                                    <td class="p-3 font-bold text-gray-800">{{ $item->quantity }}</td>
                                    <td class="p-3 text-right font-bold text-gray-900">₹{{ number_format((float) $item->total_price, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="border-t bg-gray-50/80">
                            <tr>
                                <td colspan="4" class="p-3 text-right text-gray-600 font-semibold">Subtotal:</td>
                                <td class="p-3 text-right font-bold text-gray-900">₹{{ number_format((float) $order->subtotal, 2) }}</td>
                            </tr>
                            <tr>
                                <td colspan="4" class="p-2 text-right text-gray-600 font-semibold">Shipping & Handling:</td>
                                <td class="p-2 text-right font-bold text-gray-900">
                                    {{ (float) $order->shipping_cost > 0 ? '₹'.number_format((float) $order->shipping_cost, 2) : 'FREE' }}
                                </td>
                            </tr>
                            <tr class="border-t border-gray-200">
                                <td colspan="4" class="p-3 text-right text-sm font-bold text-gray-800">Total Order Value:</td>
                                <td class="p-3 text-right text-base font-black text-forest-700">₹{{ number_format((float) $order->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Status Timeline Audit Log -->
            <div class="bg-white rounded-xl border p-6 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 border-b pb-3 mb-4">
                    Order Status & Audit History
                </h3>

                <div class="space-y-3">
                    @forelse($order->statusHistories as $history)
                        <div class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg text-xs">
                            <span class="w-6 h-6 rounded-full bg-forest-700 text-white flex items-center justify-center font-bold text-[10px] flex-shrink-0 mt-0.5">
                                ✓
                            </span>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <span class="font-bold text-gray-800 uppercase text-[11px]">{{ ucfirst(str_replace('_', ' ', $history->status)) }}</span>
                                    <span class="text-gray-400 text-[10px]">{{ $history->created_at->format('M d, Y • h:i A') }}</span>
                                </div>
                                <p class="text-gray-600 mt-1">{{ $history->comment }}</p>
                                <span class="text-[10px] text-gray-400 block mt-1">Logged by: {{ $history->created_by ?? 'System' }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 italic">No history records found.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right: Status Update Form & Customer Info (1 col) -->
        <div class="space-y-6">
            <!-- Payment & UPI Verification Panel -->
            <div class="bg-white rounded-xl border p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between border-b pb-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700">Payment & UPI Verification</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold border {{ $order->paymentStatusBadgeClasses() }}">
                        {{ ucfirst($order->payment_status) }}
                    </span>
                </div>

                <div class="text-xs space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-500">Method:</span>
                        <strong class="text-gray-800">{{ $order->paymentMethodLabel() }}</strong>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-500">Total Amount:</span>
                        <strong class="text-forest-700 text-sm font-black">₹{{ number_format((float) $order->total_amount, 2) }}</strong>
                    </div>

                    <div class="pt-2 border-t border-gray-100">
                        <span class="text-gray-500 block font-semibold mb-1">Customer Txn ID / UTR:</span>
                        @if($order->transaction_id)
                            <div class="flex items-center justify-between bg-sky-50 border border-sky-200 rounded-lg p-2.5">
                                <code class="font-mono font-bold text-sky-900 text-xs select-all">{{ $order->transaction_id }}</code>
                                <span class="text-[10px] bg-sky-100 text-sky-800 px-2 py-0.5 rounded font-semibold">Submitted</span>
                            </div>
                        @else
                            <p class="text-gray-400 italic bg-gray-50 p-2.5 rounded-lg border border-dashed text-[11px]">No transaction ID submitted yet by customer.</p>
                        @endif
                    </div>

                    @if($order->payment_verified_at)
                        <p class="text-[11px] text-emerald-700 font-medium pt-1">
                            ✓ Verified on {{ $order->payment_verified_at->format('M d, Y • h:i A') }}
                        </p>
                    @endif
                </div>

                @if($order->payment_status !== 'paid')
                    <form action="{{ route('admin.orders.verify-payment', $order) }}" method="POST"
                          onsubmit="return confirm('Confirm that payment of ₹{{ number_format((float) $order->total_amount, 2) }} has been received in bank/UPI?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="w-full bg-emerald-700 hover:bg-emerald-800 text-white font-bold py-2.5 rounded-lg text-xs shadow transition flex items-center justify-center space-x-2">
                            <span>✓ Verify Payment & Mark as Paid</span>
                        </button>
                    </form>
                @endif
            </div>

            <!-- Status Update Panel -->
            <div class="bg-white rounded-xl border p-6 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 border-b pb-3 mb-4">
                    Update Order Status & Dispatch
                </h3>

                <form action="{{ route('admin.orders.status', $order) }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PATCH')

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Order Fulfillment Status *</label>
                        <select name="status" required class="w-full text-xs p-2.5 rounded-lg border border-gray-300 bg-white">
                            <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending Confirmation</option>
                            <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing & Packing</option>
                            <option value="shipped" {{ $order->status === 'shipped' ? 'selected' : '' }}>Shipped / Dispatched</option>
                            <option value="out_for_delivery" {{ $order->status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                            <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled (Auto-Restocks Inventory)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Payment Status *</label>
                        <select name="payment_status" required class="w-full text-xs p-2.5 rounded-lg border border-gray-300 bg-white">
                            <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Payment Pending</option>
                            <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Paid</option>
                            <option value="refunded" {{ $order->payment_status === 'refunded' ? 'selected' : '' }}>Refunded</option>
                            <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Failed</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Transaction ID / UTR Reference</label>
                        <input type="text" name="transaction_id" value="{{ old('transaction_id', $order->transaction_id) }}" placeholder="e.g. 429182749182"
                               class="w-full text-xs p-2.5 rounded-lg border border-gray-300 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Courier / Shipping Carrier</label>
                        <input type="text" name="courier_name" value="{{ old('courier_name', $order->courier_name) }}" placeholder="e.g. India Post Speed Post, Delhivery, DTDC"
                               class="w-full text-xs p-2.5 rounded-lg border border-gray-300">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Tracking Number / Consignment ID</label>
                        <input type="text" name="tracking_number" value="{{ old('tracking_number', $order->tracking_number) }}" placeholder="e.g. ED123456789IN"
                               class="w-full text-xs p-2.5 rounded-lg border border-gray-300 font-mono">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Customer Visible Note (Optional)</label>
                        <input type="text" name="status_comment" placeholder="e.g. Dispatched via Speed Post, tracking activated"
                               class="w-full text-xs p-2.5 rounded-lg border border-gray-300">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1">Internal Admin Notes</label>
                        <textarea name="admin_notes" rows="2" placeholder="Private internal notes regarding packaging or payment confirmation..."
                                  class="w-full text-xs p-2.5 rounded-lg border border-gray-300">{{ old('admin_notes', $order->admin_notes) }}</textarea>
                    </div>

                    <button type="submit" class="w-full bg-forest-700 hover:bg-forest-800 text-white font-bold py-2.5 rounded-lg text-xs shadow">
                        Save Order Updates
                    </button>
                </form>
            </div>

            <!-- Customer & Shipping Info -->
            <div class="bg-white rounded-xl border p-6 shadow-sm space-y-4">
                <h3 class="text-xs font-bold uppercase tracking-wider text-gray-700 border-b pb-3">
                    Customer & Shipping Details
                </h3>

                <div class="space-y-1.5 text-xs text-gray-700">
                    <p class="font-bold text-gray-900 text-sm">{{ $order->customer_name }}</p>
                    <p class="text-gray-500">📞 {{ $order->customer_phone }}</p>
                    <p class="text-gray-500">✉️ {{ $order->customer_email }}</p>

                    <div class="pt-3 border-t border-gray-100">
                        <strong class="text-gray-500 uppercase text-[10px] block font-semibold mb-1">Shipping Address:</strong>
                        <p class="leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100">
                            {{ $order->shipping_address }}<br>
                            {{ $order->shipping_city }}, {{ $order->shipping_state }} — {{ $order->shipping_pincode }}
                        </p>
                    </div>

                    @if($order->customer_notes)
                        <div class="pt-2">
                            <strong class="text-gray-500 uppercase text-[10px] block font-semibold mb-1">Delivery Instructions:</strong>
                            <p class="italic text-gray-600 bg-sky-50 p-2.5 rounded-lg border border-sky-100">
                                "{{ $order->customer_notes }}"
                            </p>
                        </div>
                    @endif

                    @if($order->user)
                        <div class="pt-3 border-t border-gray-100 flex justify-between items-center">
                            <span class="text-gray-500">Registered Account:</span>
                            <a href="{{ route('admin.customers.show', $order->user) }}" class="font-semibold text-emerald-700 hover:underline">
                                View Customer Profile &rarr;
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
