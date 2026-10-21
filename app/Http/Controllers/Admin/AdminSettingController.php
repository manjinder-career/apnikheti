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
        if ($request->hasFile('upi_qr_code')) {
            $request->validate([
                'upi_qr_code' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:4096'],
            ]);
            $path = $request->file('upi_qr_code')->store('settings', 'public');
            SiteSetting::set('upi_qr_code', $path, 'ecommerce');
        } elseif ($request->boolean('remove_upi_qr_code')) {
            SiteSetting::set('upi_qr_code', null, 'ecommerce');
        }

        $input = $request->except(['_token', 'upi_qr_code', 'remove_upi_qr_code']);

        foreach ($input as $key => $value) {
            SiteSetting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Site & Payment settings updated successfully.');
    }
}
