<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderTrackingController extends Controller
{
    public function showForm(Request $request)
    {
        $order = null;
        $searched = false;

        if ($request->filled('order_number')) {
            $searched = true;
            $orderNumber = trim($request->string('order_number'));
            $contact = trim($request->string('contact'));

            $query = Order::with(['items.product', 'statusHistories'])
                ->where('order_number', $orderNumber);

            // If user is authenticated, they can track their own order directly
            if (Auth::check()) {
                $query->where(function ($q) use ($contact) {
                    $q->where('user_id', Auth::id())
                      ->orWhere('customer_email', $contact)
                      ->orWhere('customer_phone', $contact);
                });
            } else {
                // For public tracking, require matching email or phone for security
                $query->where(function ($q) use ($contact) {
                    $q->where('customer_email', $contact)
                      ->orWhere('customer_phone', $contact);
                });
            }

            $order = $query->first();
        }

        return view('shop.track-order', compact('order', 'searched'));
    }
}
