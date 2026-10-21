<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\FarmerStory;
use App\Models\Organization;
use App\Models\Seed;
use App\Models\SeedCategory;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $featuredSeeds = Seed::with('category')
            ->published()
            ->featured()
            ->latest()
            ->take(6)
            ->get();

        $categories = SeedCategory::withCount(['seeds' => function ($query) {
            $query->published();
        }])->orderBy('sort_order')->get();

        $latestArticles = Article::with('category')
            ->published()
            ->latest('published_at')
            ->take(3)
            ->get();

        $farmerStories = FarmerStory::published()->latest()->take(2)->get();
        $organizations = Organization::published()->take(4)->get();

        $verifiedSeedStat = SiteSetting::get('verified_seed_count', '500+');
        $displayVerifiedStat = SiteSetting::get('display_verified_stat', '1') === '1';

        $founderName = SiteSetting::get('founder_name', 'Amrit Singh Chahal');
        $founderBio = SiteSetting::get('founder_bio');

        return view('home', compact(
            'featuredSeeds',
            'categories',
            'latestArticles',
            'farmerStories',
            'organizations',
            'verifiedSeedStat',
            'displayVerifiedStat',
            'founderName',
            'founderBio'
        ));
    }
}
