<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seed;
use App\Models\SeedCategory;
use App\Models\SeedSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSubmissionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status', 'pending');
        $submissions = SeedSubmission::where('status', $status)->latest()->paginate(15);
        return view('admin.submissions.index', compact('submissions', 'status'));
    }

    public function show(SeedSubmission $submission)
    {
        $categories = SeedCategory::all();
        return view('admin.submissions.show', compact('submission', 'categories'));
    }

    public function approve(Request $request, SeedSubmission $submission)
    {
        $validated = $request->validate([
            'seed_category_id' => 'required|exists:seed_categories,id',
            'review_notes' => 'nullable|string',
        ]);

        $submission->update([
            'status' => 'approved',
            'review_notes' => $validated['review_notes'] ?? 'Approved and converted to seed record.',
            'reviewer_id' => auth()->id(),
        ]);

        // Convert contribution into a published seed record
        $slug = Str::slug($submission->seed_name_en ?? $submission->seed_name_pa);
        $originalSlug = $slug;
        $count = 1;
        while (Seed::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        Seed::create([
            'seed_category_id' => $validated['seed_category_id'],
            'slug' => $slug,
            'name_pa' => $submission->seed_name_pa,
            'name_en' => $submission->seed_name_en ?? $submission->seed_name_pa,
            'local_names' => $submission->local_names,
            'description_en' => $submission->description,
            'description_pa' => $submission->description,
            'growing_season' => $submission->growing_season,
            'traditional_uses' => $submission->traditional_uses,
            'preservation_methods' => $submission->preservation_method,
            'featured_image' => $submission->photo_path,
            'contributor_name' => $submission->contributor_name,
            'is_verified' => true,
            'publication_status' => 'published',
        ]);

        return redirect()->route('admin.submissions.index')->with('success', 'Submission approved and seed published to library.');
    }

    public function reject(Request $request, SeedSubmission $submission)
    {
        $validated = $request->validate([
            'review_notes' => 'required|string',
        ]);

        $submission->update([
            'status' => 'rejected',
            'review_notes' => $validated['review_notes'],
            'reviewer_id' => auth()->id(),
        ]);

        return redirect()->route('admin.submissions.index')->with('success', 'Submission rejected.');
    }
}
