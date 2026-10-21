<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AdminOrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['items', 'user']);

        if ($request->filled('q')) {
            $term = '%' . trim($request->string('q')) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('order_number', 'like', $term)
                  ->orWhere('customer_name', 'like', $term)
                  ->orWhere('customer_email', 'like', $term)
                  ->orWhere('customer_phone', 'like', $term)
                  ->orWhere('tracking_number', 'like', $term);
            });
        }

        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('payment_status') && $request->input('payment_status') !== 'all') {
            $query->where('payment_status', $request->string('payment_status'));
        }

        $orders = $query->latest()->paginate(15)->withQueryString();

        $counts = [
            'total' => Order::count(),
            'pending' => Order::where('status', 'pending')->count(),
            'processing' => Order::where('status', 'processing')->count(),
            'shipped' => Order::where('status', 'shipped')->count(),
            'delivered' => Order::where('status', 'delivered')->count(),
            'cancelled' => Order::where('status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'counts'));
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'statusHistories', 'user']);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,processing,shipped,out_for_delivery,delivered,cancelled'],
            'payment_status' => ['required', 'in:pending,paid,refunded,failed'],
            'transaction_id' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('orders', 'transaction_id')->ignore($order->id),
            ],
            'courier_name' => ['nullable', 'string', 'max:100'],
            'tracking_number' => ['nullable', 'string', 'max:100'],
            'admin_notes' => ['nullable', 'string', 'max:2000'],
            'status_comment' => ['nullable', 'string', 'max:1000'],
        ], [
            'transaction_id.unique' => 'This Transaction ID / UTR is already assigned to another order.',
        ]);

        $oldStatus = $order->status;
        $newStatus = $validated['status'];

        DB::transaction(function () use ($order, $validated, $oldStatus, $newStatus) {
            // Inventory restock / deduction on cancellation transitions
            if ($newStatus === 'cancelled' && $oldStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->increment('stock_quantity', $item->quantity);
                    }
                }
            } elseif ($oldStatus === 'cancelled' && $newStatus !== 'cancelled') {
                foreach ($order->items as $item) {
                    if ($item->product) {
                        $item->product->decrement('stock_quantity', $item->quantity);
                    }
                }
            }

            $paymentVerifiedAt = $order->payment_verified_at;
            if ($validated['payment_status'] === 'paid' && ! $paymentVerifiedAt) {
                $paymentVerifiedAt = now();
            }

            $order->update([
                'status' => $newStatus,
                'payment_status' => $validated['payment_status'],
                'transaction_id' => $validated['transaction_id'] ?? $order->transaction_id,
                'payment_verified_at' => $paymentVerifiedAt,
                'courier_name' => $validated['courier_name'],
                'tracking_number' => $validated['tracking_number'],
                'admin_notes' => $validated['admin_notes'],
            ]);

            $comment = $validated['status_comment'];
            if (empty($comment)) {
                $comment = 'Status updated to ' . $order->statusLabel() . ' (Payment: ' . ucfirst($validated['payment_status']) . ').';
                if (! empty($validated['transaction_id'])) {
                    $comment .= ' Ref/Txn ID: ' . $validated['transaction_id'] . '.';
                }
                if (! empty($validated['tracking_number'])) {
                    $comment .= ' Tracking #: ' . $validated['tracking_number'] . ' via ' . ($validated['courier_name'] ?? 'courier');
                }
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => $newStatus,
                'comment' => $comment,
                'created_by' => 'Admin (' . Auth::user()->name . ')',
            ]);
        });

        return back()->with('success', 'Order #' . $order->order_number . ' updated successfully.');
    }

    public function verifyPayment(Order $order)
    {
        $order->update([
            'payment_status' => 'paid',
            'payment_verified_at' => now(),
            'status' => ($order->status === 'pending') ? 'processing' : $order->status,
        ]);

        OrderStatusHistory::create([
            'order_id' => $order->id,
            'status' => $order->status,
            'comment' => 'Payment verified by Admin (' . Auth::user()->name . '). Marked as Paid. Ready for dispatch.',
            'created_by' => 'Admin (' . Auth::user()->name . ')',
        ]);

        return back()->with('success', 'Payment verified and confirmed for Order #' . $order->order_number . '.');
    }

    public function invoice(Order $order)
    {
        $order->load(['items.product', 'user']);

        return view('admin.orders.invoice', compact('order'));
    }
}
