<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FarmerStory;
use Illuminate\Http\Request;

class AdminFarmerStoryController extends Controller
{
    public function index()
    {
        $stories = FarmerStory::latest()->paginate(15);
        return view('admin.farmer-stories.index', compact('stories'));
    }

    public function updateStatus(Request $request, FarmerStory $story)
    {
        $validated = $request->validate([
            'status' => 'required|in:published,pending,archived',
        ]);

        $story->update(['status' => $validated['status']]);

        return redirect()->back()->with('success', 'Farmer story status updated to ' . $validated['status']);
    }

    public function destroy(FarmerStory $story)
    {
        $story->delete();
        return redirect()->back()->with('success', 'Farmer story deleted.');
    }
}
