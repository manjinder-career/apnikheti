<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    public function index(Request $request)
    {
        $query = Article::with('category')->published();

        if ($request->filled('q')) {
            $search = $request->get('q');
            $query->where(function ($q) use ($search) {
                $q->where('title_en', 'like', "%{$search}%")
                  ->orWhere('title_pa', 'like', "%{$search}%")
                  ->orWhere('content_en', 'like', "%{$search}%")
                  ->orWhere('content_pa', 'like', "%{$search}%");
            });
        }

        if ($request->filled('category')) {
            $categorySlug = $request->get('category');
            $query->whereHas('category', function ($q) use ($categorySlug) {
                $q->where('slug', $categorySlug);
            });
        }

        $articles = $query->latest('published_at')->paginate(6)->withQueryString();
        $categories = ArticleCategory::withCount(['articles' => function ($q) {
            $q->published();
        }])->get();

        return view('articles.index', compact('articles', 'categories'));
    }

    public function show($slug)
    {
        $article = Article::with('category')->where('slug', $slug)->published()->firstOrFail();
        $article->increment('views_count');

        $relatedArticles = Article::published()
            ->where('article_category_id', $article->article_category_id)
            ->where('id', '!=', $article->id)
            ->take(3)
            ->get();

        return view('articles.show', compact('article', 'relatedArticles'));
    }
}
