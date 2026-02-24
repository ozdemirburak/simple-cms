<?php

use App\Models\Article;
use App\Models\Category;

it('auto-generates slug from title', function () {
    $category = Category::create(['title' => 'My Category', 'is_active' => true]);

    expect($category->slug)->toBe('my-category');
});

it('has many articles', function () {
    $category = Category::create(['title' => 'Tech', 'slug' => 'tech', 'is_active' => true]);

    Article::create(['title' => 'Article 1', 'slug' => 'article-1', 'content' => 'C', 'category_id' => $category->id, 'is_published' => false]);
    Article::create(['title' => 'Article 2', 'slug' => 'article-2', 'content' => 'C', 'category_id' => $category->id, 'is_published' => false]);

    expect($category->articles)->toHaveCount(2);
});

it('generates unique slug when title duplicates', function () {
    Category::create(['title' => 'Tech', 'is_active' => true]);
    $second = Category::create(['title' => 'Tech', 'is_active' => true]);

    expect($second->slug)->toBe('tech-2');
});

it('filters active categories', function () {
    Category::create(['title' => 'Active', 'slug' => 'active', 'is_active' => true]);
    Category::create(['title' => 'Inactive', 'slug' => 'inactive', 'is_active' => false]);

    expect(Category::where('is_active', true)->count())->toBe(1);
});
