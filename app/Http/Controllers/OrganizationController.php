<?php

namespace App\Http\Controllers;

use App\Models\Organization;

class OrganizationController extends Controller
{
    public function index()
    {
        $organizations = Organization::published()->latest()->get();
        return view('organizations.index', compact('organizations'));
    }
}
