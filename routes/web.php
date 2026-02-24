<?php

use App\Http\Controllers\Frontend\ArticleController;
use App\Http\Controllers\Frontend\CategoryController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\PageController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/articles', [ArticleController::class, 'index'])->name('articles.index');
Route::get('/article/{slug}', [ArticleController::class, 'show'])->name('articles.show')->where('slug', '[a-z0-9\-]+');
Route::get('/category/{slug}', [CategoryController::class, 'show'])->name('categories.show')->where('slug', '[a-z0-9\-]+');
Route::get('/page/{slug}', [PageController::class, 'show'])->name('pages.show')->where('slug', '[a-z0-9\-]+');
