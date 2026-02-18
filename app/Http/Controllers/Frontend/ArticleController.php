<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::published()->with('category')->latest('published_at')->paginate(12);
        return view('frontend.articles.index', compact('articles'));
    }

    public function show(string $slug)
    {
        $article = Article::published()->with('category')->where('slug', $slug)->firstOrFail();
        $others = Article::published()->where('id', '!=', $article->id)->latest('published_at')->take(5)->get();

        // Record the view with details
        $article->recordView(
            request()->ip(),
            request()->userAgent(),
            request()->header('referer')
        );

        return view('frontend.articles.show', compact('article', 'others'));
    }
}
