<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class AdminSettingController extends Controller
{
    public function index()
    {
        $settings = SiteSetting::all()->pluck('value', 'key');
        return view('admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // 1. Payment Methods Activation Check
        // If payment method toggles were part of the submitted form, enforce that at least one is enabled
        if ($request->has('payment_settings_submitted') || $request->has('enable_simple_upi') || $request->has('enable_razorpay')) {
            $enableSimpleUpi = $request->boolean('enable_simple_upi');
            $enableRazorpay = $request->boolean('enable_razorpay');

            if (! $enableSimpleUpi && ! $enableRazorpay) {
                return redirect()->back()
                    ->withInput()
                    ->withErrors(['payment_methods' => 'At least one payment method (Simple UPI or Razorpay) must remain enabled so customers can checkout.']);
            }

            SiteSetting::set('enable_simple_upi', $enableSimpleUpi ? '1' : '0', 'ecommerce');
            SiteSetting::set('enable_razorpay', $enableRazorpay ? '1' : '0', 'ecommerce');
        }

        // 2. UPI QR Code Scanner Image Upload / Remove
        if ($request->hasFile('upi_qr_code')) {
            $request->validate([
                'upi_qr_code' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:4096'],
            ]);
            $path = $request->file('upi_qr_code')->store('settings', 'public');
            SiteSetting::set('upi_qr_code', $path, 'ecommerce');
        } elseif ($request->boolean('remove_upi_qr_code')) {
            SiteSetting::set('upi_qr_code', null, 'ecommerce');
        }

        // 3. Process all remaining input fields
        $input = $request->except([
            '_token',
            'upi_qr_code',
            'remove_upi_qr_code',
            'enable_simple_upi',
            'enable_razorpay',
            'payment_settings_submitted',
        ]);

        foreach ($input as $key => $value) {
            SiteSetting::set($key, is_string($value) ? trim($value) : $value);
        }

        return redirect()->back()->with('success', 'Site & Payment settings updated successfully.');
    }
}
