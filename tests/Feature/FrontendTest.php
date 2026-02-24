<?php

use App\Models\Article;
use App\Models\Category;
use App\Models\Page;

it('loads the homepage', function () {
    $this->get('/')->assertOk()->assertSee('Simple CMS');
});

it('displays published articles on homepage', function () {
    $category = Category::create(['title' => 'Technology', 'slug' => 'technology', 'is_active' => true]);

    foreach (range(1, 3) as $i) {
        Article::create([
            'title' => "Test Article $i",
            'slug' => "test-article-$i",
            'content' => 'Test content',
            'category_id' => $category->id,
            'is_published' => true,
            'published_at' => now(),
        ]);
    }

    $this->get('/')->assertOk()->assertSee('Test Article 1');
});

it('loads articles index page', function () {
    $this->get('/articles')->assertOk()->assertSee('All Articles');
});

it('shows a published article', function () {
    $category = Category::create(['title' => 'Technology', 'slug' => 'technology', 'is_active' => true]);

    Article::create([
        'title' => 'My Test Article',
        'slug' => 'my-test-article',
        'content' => '<p>Article content here</p>',
        'category_id' => $category->id,
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->get('/article/my-test-article')
        ->assertOk()
        ->assertSee('My Test Article')
        ->assertSee('Article content here');
});

it('records article views', function () {
    $article = Article::create([
        'title' => 'View Test',
        'slug' => 'view-test',
        'content' => 'Content',
        'is_published' => true,
        'published_at' => now(),
    ]);

    expect($article->views()->count())->toBe(0);

    $this->get('/article/view-test');

    expect($article->views()->count())->toBe(1);
});

it('deduplicates article views per IP', function () {
    $article = Article::create([
        'title' => 'Dedup Test',
        'slug' => 'dedup-test',
        'content' => 'Content',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->get('/article/dedup-test');
    $this->get('/article/dedup-test');

    expect($article->views()->count())->toBe(1);
});

it('returns 404 for unpublished articles', function () {
    Article::create([
        'title' => 'Draft Article',
        'slug' => 'draft-article',
        'content' => 'Content',
        'is_published' => false,
    ]);

    $this->get('/article/draft-article')->assertNotFound();
});

it('returns 404 for nonexistent articles', function () {
    $this->get('/article/does-not-exist')->assertNotFound();
});

it('shows category page with articles', function () {
    $category = Category::create(['title' => 'Design', 'slug' => 'design', 'is_active' => true]);

    Article::create([
        'title' => 'Design Article',
        'slug' => 'design-article',
        'content' => 'Content',
        'category_id' => $category->id,
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->get('/category/design')
        ->assertOk()
        ->assertSee('Design')
        ->assertSee('Design Article');
});

it('returns 404 for inactive categories', function () {
    Category::create(['title' => 'Inactive', 'slug' => 'inactive', 'is_active' => false]);

    $this->get('/category/inactive')->assertNotFound();
});

it('shows a published page', function () {
    Page::create([
        'title' => 'About Us',
        'slug' => 'about-us',
        'content' => '<p>About us content</p>',
        'is_published' => true,
    ]);

    $this->get('/page/about-us')
        ->assertOk()
        ->assertSee('About Us')
        ->assertSee('About us content');
});

it('returns 404 for unpublished pages', function () {
    Page::create([
        'title' => 'Draft Page',
        'slug' => 'draft-page',
        'content' => 'Content',
        'is_published' => false,
    ]);

    $this->get('/page/draft-page')->assertNotFound();
});

it('shows published pages in navigation', function () {
    Page::create([
        'title' => 'Contact',
        'slug' => 'contact',
        'content' => 'Contact us',
        'is_published' => true,
    ]);

    $this->get('/')->assertOk()->assertSee('Contact');
});
