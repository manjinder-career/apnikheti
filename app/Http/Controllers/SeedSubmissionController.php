<?php

namespace App\Http\Controllers;

use App\Models\SeedCategory;
use App\Models\SeedSubmission;
use Illuminate\Http\Request;

class SeedSubmissionController extends Controller
{
    public function create()
    {
        $categories = SeedCategory::orderBy('sort_order')->get();
        return view('seeds.contribute', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'contributor_name' => 'required|string|max:255',
            'village' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'seed_name_pa' => 'required|string|max:255',
            'seed_name_en' => 'nullable|string|max:255',
            'crop_category_name' => 'nullable|string|max:255',
            'local_names' => 'nullable|string|max:255',
            'description' => 'required|string',
            'traditional_uses' => 'nullable|string',
            'growing_season' => 'nullable|string|max:255',
            'preservation_method' => 'nullable|string',
            'duration_known' => 'nullable|string|max:255',
            'contact_consent' => 'nullable|boolean',
            'display_consent' => 'nullable|boolean',
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('submissions', 'public');
        }

        SeedSubmission::create([
            'contributor_name' => $validated['contributor_name'],
            'village' => $validated['village'] ?? null,
            'district' => $validated['district'] ?? null,
            'state' => $validated['state'] ?? 'Punjab',
            'phone' => $validated['phone'] ?? null,
            'email' => $validated['email'] ?? null,
            'seed_name_pa' => $validated['seed_name_pa'],
            'seed_name_en' => $validated['seed_name_en'] ?? null,
            'crop_category_name' => $validated['crop_category_name'] ?? null,
            'local_names' => $validated['local_names'] ?? null,
            'description' => $validated['description'],
            'traditional_uses' => $validated['traditional_uses'] ?? null,
            'growing_season' => $validated['growing_season'] ?? null,
            'preservation_method' => $validated['preservation_method'] ?? null,
            'duration_known' => $validated['duration_known'] ?? null,
            'photo_path' => $photoPath,
            'contact_consent' => $request->has('contact_consent'),
            'display_consent' => $request->has('display_consent'),
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Thank you! Your seed information contribution has been recorded and submitted for administrative review.');
    }
}
