<?php

namespace App\Http\Controllers;

use App\Models\Organization;
use App\Models\Seed;
use App\Models\SiteSetting;

class AboutController extends Controller
{
    public function index()
    {
        $founderName = SiteSetting::get('founder_name', 'Amrit Singh Chahal');
        $founderTitle = SiteSetting::get('founder_title', 'Seed Conservationist & Natural Farming Advocate');
        $founderBio = SiteSetting::get('founder_bio');
        $verifiedCount = SiteSetting::get('verified_seed_count', '500+');
        $organizations = Organization::published()->get();

        $totalSeeds = Seed::published()->count();

        return view('about', compact(
            'founderName',
            'founderTitle',
            'founderBio',
            'verifiedCount',
            'organizations',
            'totalSeeds'
        ));
    }
}
