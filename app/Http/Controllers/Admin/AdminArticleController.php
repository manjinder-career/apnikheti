<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\ArticleCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminArticleController extends Controller
{
    public function index()
    {
        $articles = Article::with('category')->latest()->paginate(15);
        return view('admin.articles.index', compact('articles'));
    }

    public function create()
    {
        $categories = ArticleCategory::all();
        return view('admin.articles.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'article_category_id' => 'required|exists:article_categories,id',
            'title_en' => 'required|string|max:255',
            'title_pa' => 'required|string|max:255',
            'title_hi' => 'nullable|string|max:255',
            'excerpt_en' => 'nullable|string',
            'excerpt_pa' => 'nullable|string',
            'excerpt_hi' => 'nullable|string',
            'content_en' => 'required|string',
            'content_pa' => 'nullable|string',
            'content_hi' => 'nullable|string',
            'author_name' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        $slug = Str::slug($validated['title_en']);
        $originalSlug = $slug;
        $count = 1;
        while (Article::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $count++;
        }

        $imagePath = null;
        if ($request->hasFile('featured_image')) {
            $imagePath = $request->file('featured_image')->store('articles', 'public');
        }

        Article::create([
            'article_category_id' => $validated['article_category_id'],
            'slug' => $slug,
            'title_en' => $validated['title_en'],
            'title_pa' => $validated['title_pa'],
            'title_hi' => $validated['title_hi'] ?? null,
            'excerpt_en' => $validated['excerpt_en'] ?? null,
            'excerpt_pa' => $validated['excerpt_pa'] ?? null,
            'excerpt_hi' => $validated['excerpt_hi'] ?? null,
            'content_en' => $validated['content_en'],
            'content_pa' => $validated['content_pa'] ?? null,
            'content_hi' => $validated['content_hi'] ?? null,
            'author_name' => $validated['author_name'] ?? 'Apni Kheti Team',
            'featured_image' => $imagePath,
            'is_featured' => $request->has('is_featured'),
            'is_published' => $request->has('is_published'),
            'published_at' => $request->has('is_published') ? now() : null,
        ]);

        return redirect()->route('admin.articles.index')->with('success', 'Article published successfully.');
    }

    public function edit(Article $article)
    {
        $categories = ArticleCategory::all();
        return view('admin.articles.edit', compact('article', 'categories'));
    }

    public function update(Request $request, Article $article)
    {
        $validated = $request->validate([
            'article_category_id' => 'required|exists:article_categories,id',
            'title_en' => 'required|string|max:255',
            'title_pa' => 'required|string|max:255',
            'title_hi' => 'nullable|string|max:255',
            'excerpt_en' => 'nullable|string',
            'excerpt_pa' => 'nullable|string',
            'excerpt_hi' => 'nullable|string',
            'content_en' => 'required|string',
            'content_pa' => 'nullable|string',
            'content_hi' => 'nullable|string',
            'author_name' => 'nullable|string|max:255',
            'is_featured' => 'nullable|boolean',
            'is_published' => 'nullable|boolean',
            'featured_image' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:4096',
        ]);

        if ($request->hasFile('featured_image')) {
            $validated['featured_image'] = $request->file('featured_image')->store('articles', 'public');
        }

        $validated['is_featured'] = $request->has('is_featured');
        $validated['is_published'] = $request->has('is_published');
        if ($validated['is_published'] && !$article->published_at) {
            $validated['published_at'] = now();
        }

        $article->update($validated);

        return redirect()->route('admin.articles.index')->with('success', 'Article updated successfully.');
    }

    public function destroy(Article $article)
    {
        $article->delete();
        return redirect()->route('admin.articles.index')->with('success', 'Article deleted.');
    }
}
