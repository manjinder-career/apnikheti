<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderStatusHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CustomerAccountController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $recentOrders = $user->orders()->with('items.product')->latest()->take(5)->get();
        $totalOrdersCount = $user->orders()->count();
        $pendingOrdersCount = $user->orders()->whereIn('status', ['pending', 'processing'])->count();
        $totalSpent = $user->orders()->where('payment_status', 'paid')->sum('total_amount');

        return view('account.dashboard', compact('user', 'recentOrders', 'totalOrdersCount', 'pendingOrdersCount', 'totalSpent'));
    }

    public function orders(Request $request)
    {
        $user = Auth::user();
        $query = $user->orders()->with('items');

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        $orders = $query->latest()->paginate(10)->withQueryString();

        return view('account.orders.index', compact('orders'));
    }

    public function showOrder(string $orderNumber)
    {
        $user = Auth::user();
        $order = $user->orders()
            ->with(['items.product', 'statusHistories'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('account.orders.show', compact('order'));
    }

    public function cancelOrder(Request $request, string $orderNumber)
    {
        $user = Auth::user();
        $order = $user->orders()->where('order_number', $orderNumber)->firstOrFail();

        if (! $order->canCancel()) {
            return back()->with('error', 'This order cannot be cancelled as it is already being processed or shipped.');
        }

        DB::transaction(function () use ($order, $request) {
            $order->update([
                'status' => 'cancelled',
            ]);

            // Restock items
            foreach ($order->items as $item) {
                if ($item->product) {
                    $item->product->increment('stock_quantity', $item->quantity);
                }
            }

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'status' => 'cancelled',
                'comment' => $request->input('reason', 'Cancelled by customer.'),
                'created_by' => 'Customer',
            ]);
        });

        return back()->with('success', 'Order #' . $order->order_number . ' has been cancelled successfully.');
    }

    public function profile()
    {
        $user = Auth::user();
        return view('account.profile', compact('user'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'address' => ['required', 'string', 'max:500'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'pincode' => ['required', 'string', 'max:20'],
            'current_password' => ['nullable', 'required_with:new_password', 'current_password'],
            'new_password' => ['nullable', 'confirmed', Password::min(8)],
        ]);

        $user->name = $validated['name'];
        $user->phone = $validated['phone'];
        $user->address = $validated['address'];
        $user->city = $validated['city'];
        $user->state = $validated['state'];
        $user->pincode = $validated['pincode'];

        if (! empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return back()->with('success', 'Your profile and default shipping address have been updated.');
    }
}
