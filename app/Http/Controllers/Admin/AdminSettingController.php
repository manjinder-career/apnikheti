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
        $input = $request->except(['_token']);

        foreach ($input as $key => $value) {
            SiteSetting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Site settings updated successfully.');
    }
}
