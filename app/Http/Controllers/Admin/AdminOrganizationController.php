<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Organization;
use Illuminate\Http\Request;

class AdminOrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::latest()->paginate(15);
        return view('admin.organizations.index', compact('organizations'));
    }

    public function create()
    {
        return view('admin.organizations.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'collaboration_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'projects_summary' => 'nullable|string',
            'is_published' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:2048',
        ]);

        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('organizations', 'public');
        }

        Organization::create([
            'name' => $validated['name'],
            'website_url' => $validated['website_url'] ?? null,
            'collaboration_type' => $validated['collaboration_type'] ?? null,
            'description' => $validated['description'] ?? null,
            'projects_summary' => $validated['projects_summary'] ?? null,
            'is_published' => $request->has('is_published'),
            'logo_path' => $logoPath,
        ]);

        return redirect()->route('admin.organizations.index')->with('success', 'Organization profile created.');
    }

    public function edit(Organization $organization)
    {
        return view('admin.organizations.edit', compact('organization'));
    }

    public function update(Request $request, Organization $organization)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'website_url' => 'nullable|url|max:255',
            'collaboration_type' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'projects_summary' => 'nullable|string',
            'is_published' => 'nullable|boolean',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,webp,svg|max:2048',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo_path'] = $request->file('logo')->store('organizations', 'public');
        }

        $validated['is_published'] = $request->has('is_published');

        $organization->update($validated);

        return redirect()->route('admin.organizations.index')->with('success', 'Organization updated.');
    }

    public function destroy(Organization $organization)
    {
        $organization->delete();
        return redirect()->route('admin.organizations.index')->with('success', 'Organization removed.');
    }
}
