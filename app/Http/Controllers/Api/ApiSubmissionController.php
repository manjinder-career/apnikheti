<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SeedSubmission;
use Illuminate\Http\Request;

class ApiSubmissionController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'contributor_name' => 'required|string|max:255',
            'village' => 'nullable|string|max:255',
            'district' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:50',
            'seed_name_pa' => 'required|string|max:255',
            'seed_name_en' => 'nullable|string|max:255',
            'description' => 'required|string',
        ]);

        $submission = SeedSubmission::create(array_merge($validated, ['status' => 'pending']));

        return response()->json([
            'status' => 'success',
            'message' => 'Seed contribution submitted for review.',
            'data' => $submission,
        ], 201);
    }
}
