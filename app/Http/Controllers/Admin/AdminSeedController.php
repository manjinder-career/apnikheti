<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Seed;
use App\Models\SeedCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminSeedController extends Controller
{
    public function index(Request $request)
    {
        $query = Seed::with('category');

        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function ($q) use ($search) {
                $q->where('name_pa', 'like', "%{$search}%")
                  ->orWhere('name_en', 'like', "%{$search}%")
                  ->orWhere('scientific_name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category_id')) {
            $query->where('seed_category_id', $request->get('category_id'));
        }

        $seeds = $query->latest()->paginate(15)->withQueryString();
        $categories = SeedCategory::all();

        return view('admin.seeds.index', compact('seeds', 'categories'));
    }

    public function create()
    {
        $categories = SeedCategory::all();
        return view('admin.seeds.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'seed_category_id' => 'required|exists:seed_categories,id',
            'name_pa' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'name_hi' => 'nullable|string|max:255',
            'local_names' => 'nullable|string|max:255',
            'scientific_name' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_pa' => 'nullable|string',
            'description_hi' => 'nullable|string',
            'region_of_origin' => 'nullable|string|max:255',
            'growing_season' => 'nullable|string|max:255',
            'sowing_period' => 'nullable|string|max:255',
            'soil_preferences' => 'nullable|string|max:255',
            'water_requirements' => 'nullable|string|max:255',
            'crop_duration' => 'nullable|string|max:255',
            'traditional_uses' => 'nullable|string',
            'culinary_uses' => 'nullable|string',
            'farmer_observations' => 'nullable|string',
            'preservation_methods' => 'nullable|string',
            'contributor_name' => 'nullable|string|max:255',
            'publication_status' => 'required|in:published,draft,archived',
            'is_featured' => 'nullable|boolean',
            'is_verified' => 'nullable|boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        $slug = Str::slug($validated['name_en']);
        $originalSlug = $slug;
        $count = 1;
        while (Seed::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('seeds', 'public');
        }

        $validated['slug'] = $slug;
        $validated['featured_image'] = $imagePath;
        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_verified'] = $request->has('is_verified');
        $validated['user_id'] = auth()->id();

        Seed::create($validated);

        return redirect()->route('admin.seeds.index')->with('success', 'Seed record created successfully.');
    }

    public function edit(Seed $seed)
    {
        $categories = SeedCategory::all();
        return view('admin.seeds.edit', compact('seed', 'categories'));
    }

    public function update(Request $request, Seed $seed)
    {
        $validated = $request->validate([
            'seed_category_id' => 'required|exists:seed_categories,id',
            'name_pa' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'name_hi' => 'nullable|string|max:255',
            'local_names' => 'nullable|string|max:255',
            'scientific_name' => 'nullable|string|max:255',
            'description_en' => 'nullable|string',
            'description_pa' => 'nullable|string',
            'description_hi' => 'nullable|string',
            'region_of_origin' => 'nullable|string|max:255',
            'growing_season' => 'nullable|string|max:255',
            'sowing_period' => 'nullable|string|max:255',
            'soil_preferences' => 'nullable|string|max:255',
            'water_requirements' => 'nullable|string|max:255',
            'crop_duration' => 'nullable|string|max:255',
            'traditional_uses' => 'nullable|string',
            'culinary_uses' => 'nullable|string',
            'farmer_observations' => 'nullable|string',
            'preservation_methods' => 'nullable|string',
            'contributor_name' => 'nullable|string|max:255',
            'publication_status' => 'required|in:published,draft,archived',
            'is_featured' => 'nullable|boolean',
            'is_verified' => 'nullable|boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('seeds', 'public');
        }

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_verified'] = $request->has('is_verified');

        $seed->update($validated);

        return redirect()->route('admin.seeds.index')->with('success', 'Seed record updated successfully.');
    }

    public function destroy(Seed $seed)
    {
        $seed->delete();
        return redirect()->route('admin.seeds.index')->with('success', 'Seed record moved to archive.');
    }

    public function exportCsv()
    {
        $seeds = Seed::with('category')->get();

        $filename = "seeds_export_" . date('Y-m-d') . ".csv";

        $headers = [
            "Content-Type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=\"$filename\"",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($seeds) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility with Gurmukhi Punjabi text
            fputs($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'ID', 'Name (Punjabi)', 'Name (English)', 'Category', 'Scientific Name',
                'Local Names', 'Region of Origin', 'Growing Season', 'Soil Preferences',
                'Water Requirements', 'Is Verified', 'Is Featured', 'Publication Status'
            ]);

            foreach ($seeds as $seed) {
                fputcsv($file, [
                    $seed->id,
                    $seed->name_pa,
                    $seed->name_en,
                    $seed->category ? $seed->category->name_en : '',
                    $seed->scientific_name,
                    $seed->local_names,
                    $seed->region_of_origin,
                    $seed->growing_season,
                    $seed->soil_preferences,
                    $seed->water_requirements,
                    $seed->is_verified ? 'Yes' : 'No',
                    $seed->is_featured ? 'Yes' : 'No',
                    $seed->publication_status,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function importCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:5120',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');

        // Check for BOM
        $bom = fread($handle, 3);
        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        $header = fgetcsv($handle);
        $importedCount = 0;

        $defaultCategory = SeedCategory::first();

        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 3) {
                $namePa = trim($row[0] ?? '');
                $nameEn = trim($row[1] ?? '');
                $categoryName = trim($row[2] ?? '');

                if (empty($nameEn)) {
                    continue;
                }

                $category = SeedCategory::where('name_en', 'like', "%{$categoryName}%")->first() ?? $defaultCategory;

                $slug = Str::slug($nameEn);
                $originalSlug = $slug;
                $count = 1;
                while (Seed::where('slug', $slug)->exists()) {
                    $slug = $originalSlug . '-' . $count++;
                }

                Seed::create([
                    'seed_category_id' => $category->id,
                    'slug' => $slug,
                    'name_pa' => !empty($namePa) ? $namePa : $nameEn,
                    'name_en' => $nameEn,
                    'scientific_name' => $row[3] ?? null,
                    'local_names' => $row[4] ?? null,
                    'region_of_origin' => $row[5] ?? null,
                    'growing_season' => $row[6] ?? null,
                    'soil_preferences' => $row[7] ?? null,
                    'water_requirements' => $row[8] ?? null,
                    'is_verified' => true,
                    'publication_status' => 'published',
                ]);

                $importedCount++;
            }
        }

        fclose($handle);

        return redirect()->route('admin.seeds.index')->with('success', "Successfully imported {$importedCount} seed records from CSV.");
    }
}
