<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;

class ArticleController extends Controller
{
    public function index()
    {
        $articles = Article::published()->with('category')->latest('published_at')->paginate(12);
        $categories = Category::where('is_active', true)
            ->whereHas('articles', fn ($q) => $q->published())
            ->withCount(['articles' => fn ($q) => $q->published()])
            ->get();

        return view('frontend.articles.index', compact('articles', 'categories'));
    }

    public function show(string $slug)
    {
        $article = Article::published()->with('category')->withCount('views')->where('slug', $slug)->firstOrFail();
        $others = Article::published()->where('id', '!=', $article->id)->latest('published_at')->take(5)->get();

        // Record the view with details (deduplicated per IP per 30 minutes)
        $ip = filter_var(request()->ip(), FILTER_VALIDATE_IP) ? request()->ip() : null;
        $cacheKey = 'article_view:'.$article->id.':'.md5($ip ?? 'unknown');
        if (cache()->add($cacheKey, true, now()->addMinutes(30))) {
            $article->recordView(
                $ip,
                request()->userAgent(),
                request()->header('referer')
            );
        }

        return view('frontend.articles.show', compact('article', 'others'));
    }
}
