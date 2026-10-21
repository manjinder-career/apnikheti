<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $order->order_number }} — Apni Kheti Apne Beej</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { font-size: 12px; }
        }
    </style>
</head>
<body class="bg-gray-100 p-6 md:p-12 font-sans text-gray-800">

    <div class="max-w-3xl mx-auto bg-white p-8 md:p-12 rounded-xl shadow border">
        <!-- Print / Action bar -->
        <div class="no-print flex justify-between items-center mb-8 border-b pb-4">
            <a href="{{ route('admin.orders.show', $order) }}" class="text-xs text-gray-500 hover:underline">&larr; Return to Order Details</a>
            <button onclick="window.print()" class="bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs px-5 py-2 rounded-lg shadow">
                🖨️ Print Packing Slip / Invoice
            </button>
        </div>

        <!-- Invoice Header -->
        <div class="flex justify-between items-start border-b pb-8">
            <div>
                <h1 class="text-2xl font-black text-emerald-800">ਆਪਣੀ ਖੇਤੀ ਆਪਣੇ ਬੀਜ</h1>
                <p class="text-xs uppercase font-bold tracking-widest text-gray-500 mt-0.5">Apni Kheti Apne Beej</p>
                <p class="text-xs text-gray-500 mt-2">Traditional Seeds Conservation Initiative<br>Punjab, India • Contact: info@apnikheti.org</p>
            </div>
            <div class="text-right">
                <span class="inline-block bg-emerald-100 text-emerald-800 text-xs font-bold uppercase px-3 py-1 rounded">Invoice & Packing Slip</span>
                <p class="text-sm font-mono font-bold text-gray-900 mt-3">#{{ $order->order_number }}</p>
                <p class="text-xs text-gray-500">Date: {{ $order->created_at->format('M d, Y') }}</p>
                <p class="text-xs text-gray-500">Payment: {{ strtoupper($order->payment_method) }} ({{ ucfirst($order->payment_status) }})</p>
            </div>
        </div>

        <!-- Recipient & Delivery Information -->
        <div class="grid grid-cols-2 gap-8 py-6 border-b text-xs">
            <div>
                <span class="text-gray-400 uppercase font-bold tracking-wider block mb-1">Deliver To:</span>
                <p class="font-bold text-gray-900 text-sm">{{ $order->customer_name }}</p>
                <p class="text-gray-600 mt-1 leading-relaxed">
                    {{ $order->shipping_address }}<br>
                    {{ $order->shipping_city }}, {{ $order->shipping_state }} — {{ $order->shipping_pincode }}
                </p>
                <p class="text-gray-600 mt-1 font-medium">📞 {{ $order->customer_phone }}</p>
                <p class="text-gray-500">✉️ {{ $order->customer_email }}</p>
            </div>

            <div class="text-right">
                <span class="text-gray-400 uppercase font-bold tracking-wider block mb-1">Dispatch Details:</span>
                <p class="text-gray-600">Carrier: <strong>{{ $order->courier_name ?? 'Speed Post / Courier' }}</strong></p>
                @if($order->tracking_number)
                    <p class="text-gray-600 font-mono">Tracking #: <strong>{{ $order->tracking_number }}</strong></p>
                @endif
                <p class="text-gray-600 mt-1">Status: <strong>{{ $order->statusLabel() }}</strong></p>
                @if($order->customer_notes)
                    <p class="text-gray-500 italic mt-2">"{{ $order->customer_notes }}"</p>
                @endif
            </div>
        </div>

        <!-- Line Items -->
        <div class="py-6">
            <table class="w-full text-left text-xs">
                <thead>
                    <tr class="border-b uppercase text-gray-400 text-[10px]">
                        <th class="py-2">Item Description</th>
                        <th class="py-2">SKU</th>
                        <th class="py-2 text-right">Unit Price</th>
                        <th class="py-2 text-center">Qty</th>
                        <th class="py-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($order->items as $item)
                        <tr>
                            <td class="py-3 font-semibold text-gray-900">{{ $item->product_name }}</td>
                            <td class="py-3 font-mono text-gray-500">{{ $item->product_sku ?? '-' }}</td>
                            <td class="py-3 text-right">₹{{ number_format((float) $item->unit_price, 2) }}</td>
                            <td class="py-3 text-center font-bold">{{ $item->quantity }}</td>
                            <td class="py-3 text-right font-bold text-gray-900">₹{{ number_format((float) $item->total_price, 2) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="border-t pt-4 text-xs space-y-1.5 flex flex-col items-end">
            <div class="w-64 flex justify-between text-gray-600">
                <span>Subtotal:</span>
                <span class="font-semibold text-gray-900">₹{{ number_format((float) $order->subtotal, 2) }}</span>
            </div>
            <div class="w-64 flex justify-between text-gray-600">
                <span>Shipping:</span>
                <span class="font-semibold text-gray-900">{{ (float) $order->shipping_cost > 0 ? '₹'.number_format((float) $order->shipping_cost, 2) : 'FREE' }}</span>
            </div>
            <div class="w-64 flex justify-between border-t border-gray-300 pt-2 text-sm font-bold text-gray-900">
                <span>Grand Total:</span>
                <span class="text-base text-emerald-800">₹{{ number_format((float) $order->total_amount, 2) }}</span>
            </div>
        </div>

        <!-- Footer notes -->
        <div class="border-t mt-10 pt-6 text-center text-[11px] text-gray-400 space-y-1">
            <p class="font-semibold text-gray-600">Thank you for supporting traditional seed preservation and biodiversity in Punjab!</p>
            <p>Our farming. Our seeds. Our future. • ਆਪਣੀ ਖੇਤੀ ਆਪਣੇ ਬੀਜ</p>
        </div>
    </div>

</body>
</html>
