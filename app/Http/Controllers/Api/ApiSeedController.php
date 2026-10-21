<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Seed;
use App\Models\SeedCategory;
use Illuminate\Http\Request;

class ApiSeedController extends Controller
{
    public function index(Request $request)
    {
        $query = Seed::with('category')->published();

        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function ($q) use ($search) {
                $q->where('name_pa', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('scientific_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->get('category'));
            });
        }

        $seeds = $query->paginate($request->get('per_page', 15));

        return response()->json([
            'status' => 'success',
            'data' => $seeds,
        ]);
    }

    public function categories()
    {
        $categories = SeedCategory::withCount(['seeds' => function ($q) {
            $q->published();
        }])->get();

        return response()->json([
            'status' => 'success',
            'data' => $categories,
        ]);
    }

    public function show($slug)
    {
        $seed = Seed::with(['category', 'photos'])->where('slug', $slug)->published()->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $seed,
        ]);
    }

    public function stats()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'total_published_seeds' => Seed::published()->count(),
                'total_verified_seeds' => Seed::published()->verified()->count(),
                'categories_count' => SeedCategory::count(),
            ],
        ]);
    }
}
