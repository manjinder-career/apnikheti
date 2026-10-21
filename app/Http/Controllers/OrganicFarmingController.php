<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;

class OrganicFarmingController extends Controller
{
    public function index()
    {
        $farmingCategory = ArticleCategory::where('slug', 'organic-natural-farming')->first();
        
        $articles = Article::published()
            ->when($farmingCategory, function ($q) use ($farmingCategory) {
                $q->where('article_category_id', $farmingCategory->id);
            })
            ->latest()
            ->paginate(6);

        return view('organic-farming', compact('articles', 'farmingCategory'));
    }
}
