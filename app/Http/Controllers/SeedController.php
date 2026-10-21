<?php

namespace App\Http\Controllers;

use App\Models\Seed;
use App\Models\SeedCategory;
use Illuminate\Http\Request;

class SeedController extends Controller
{
    public function index(Request $request)
    {
        $query = Seed::with(['category', 'photos'])->published();

        // Search by keyword in Punjabi, English, Hindi, or local names
        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function ($q) use ($search) {
                $q->where('name_pa', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('name_hi', 'like', "%{$search}%")
                  ->orWhere('local_names', 'like', "%{$search}%")
                  ->orWhere('scientific_name', 'like', "%{$search}%")
                  ->orWhere('description_en', 'like', "%{$search}%")
                  ->orWhere('description_pa', 'like', "%{$search}%");
            });
        }

        // Category filter
        if ($request->filled('category')) {
            $categorySlug = $request->get('category');
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        // Season filter
        if ($request->filled('season')) {
            $query->where('growing_season', 'like', "%" . $request->get('season') . "%");
        }

        // Region filter
        if ($request->filled('region')) {
            $query->where('region_of_origin', 'like', "%" . $request->get('region') . "%");
        }

        // Verified filter
        if ($request->has('verified') && $request->get('verified') == '1') {
            $query->where('is_verified', true);
        }

        $seeds = $query->latest()->paginate(9)->withQueryString();

        $categories = SeedCategory::withCount(['seeds' => function ($q) {
            $q->published();
        }])->get();

        $seasons = ['Rabi (Winter)', 'Kharif (Monsoon)', 'Zaid (Summer)'];

        return view('seeds.index', compact('seeds', 'categories', 'seasons'));
    }

    public function show($slug)
    {
        $seed = Seed::with(['category', 'photos'])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        $relatedSeeds = Seed::with('category')
            ->published()
            ->where('seed_category_id', $seed->seed_category_id)
            ->where('id', '!=', $seed->id)
            ->take(3)
            ->get();

        return view('seeds.show', compact('seed', 'relatedSeeds'));
    }
}
