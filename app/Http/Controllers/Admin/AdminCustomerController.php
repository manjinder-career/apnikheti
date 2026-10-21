<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminCustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('orders')
            ->withSum(['orders' => function ($q) {
                $q->where('payment_status', 'paid');
            }], 'total_amount')
            ->where(function ($q) {
                $q->where('role', 'customer')
                  ->orWhere('role', 'user')
                  ->orWhereHas('orders');
            });

        if ($request->filled('q')) {
            $term = '%' . trim($request->string('q')) . '%';
            $query->where(function ($q) use ($term) {
                $q->where('name', 'like', $term)
                  ->orWhere('email', 'like', $term)
                  ->orWhere('phone', 'like', $term)
                  ->orWhere('city', 'like', $term);
            });
        }

        $customers = $query->latest()->paginate(15)->withQueryString();
        $totalCustomers = User::whereIn('role', ['customer', 'user'])->orWhereHas('orders')->count();

        return view('admin.customers.index', compact('customers', 'totalCustomers'));
    }

    public function show(User $customer)
    {
        $customer->loadCount('orders');
        $orders = $customer->orders()->with('items')->latest()->paginate(10);
        $totalSpent = $customer->orders()->where('payment_status', 'paid')->sum('total_amount');
        $pendingOrdersCount = $customer->orders()->whereIn('status', ['pending', 'processing'])->count();

        return view('admin.customers.show', compact('customer', 'orders', 'totalSpent', 'pendingOrdersCount'));
    }

    public function edit(User $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, User $customer)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($customer->id)],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string', 'max:500'],
            'city' => ['nullable', 'string', 'max:100'],
            'state' => ['nullable', 'string', 'max:100'],
            'pincode' => ['nullable', 'string', 'max:20'],
        ]);

        $customer->update($validated);

        return redirect()->route('admin.customers.show', $customer)->with('success', 'Customer profile updated.');
    }
}
