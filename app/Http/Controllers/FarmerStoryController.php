<?php

namespace App\Http\Controllers;

use App\Models\FarmerStory;
use Illuminate\Http\Request;

class FarmerStoryController extends Controller
{
    public function index()
    {
        $stories = FarmerStory::published()->latest()->paginate(6);
        return view('farmer-stories.index', compact('stories'));
    }

    public function create()
    {
        return view('farmer-stories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'farmer_name' => 'required|string|max:255',
            'village' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'crop_name' => 'nullable|string|max:255',
            'farming_practice' => 'nullable|string|max:255',
            'description' => 'required|string',
            'contact_info_private' => 'nullable|string|max:255',
            'display_consent' => 'nullable|boolean',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:3072',
        ]);

        $photoPath = null;
        if ($request->hasFile('photo')) {
            $photoPath = $request->file('photo')->store('farmer_stories', 'public');
        }

        FarmerStory::create([
            'farmer_name' => $validated['farmer_name'],
            'village' => $validated['village'] ?? null,
            'district' => $validated['district'] ?? null,
            'crop_name' => $validated['crop_name'] ?? null,
            'farming_practice' => $validated['farming_practice'] ?? null,
            'description' => $validated['description'],
            'contact_info_private' => $validated['contact_info_private'] ?? null,
            'display_consent' => $request->has('display_consent'),
            'photo_path' => $photoPath,
            'status' => 'pending',
        ]);

        return redirect()->back()->with('success', 'Thank you! Your story has been submitted for administrative review.');
    }
}
