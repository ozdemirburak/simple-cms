<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Category;
use App\Models\Page;

class HomeController extends Controller
{
    public function index()
    {
        $articles = Article::published()->with('category')->latest('published_at')->take(6)->get();
        $pages = Page::published()->roots()->orderBy('sort_order')->get();
        $categories = Category::where('is_active', true)->whereHas('articles', fn($q) => $q->published())->withCount(['articles' => fn($q) => $q->published()])->get();
        return view('frontend.home', compact('articles', 'pages', 'categories'));
    }
}
