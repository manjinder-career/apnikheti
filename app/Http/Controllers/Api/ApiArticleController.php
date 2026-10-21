<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ApiArticleController extends Controller
{
    public function index(Request $request)
    {
        $articles = Article::with('category')->published()->latest()->paginate(15);
        return response()->json([
            'status' => 'success',
            'data' => $articles,
        ]);
    }

    public function show($slug)
    {
        $article = Article::with('category')->where('slug', $slug)->published()->firstOrFail();
        return response()->json([
            'status' => 'success',
            'data' => $article,
        ]);
    }
}
