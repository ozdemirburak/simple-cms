<?php

use App\Models\Article;
use App\Models\Page;

it('rejects invalid characters in slug routes', function () {
    $this->get('/article/<script>alert(1)</script>')->assertNotFound();
});

it('rejects path traversal in slug routes', function () {
    $this->get('/article/../../../etc/passwd')->assertNotFound();
});

it('accepts valid slugs', function () {
    Article::create([
        'title' => 'Valid Slug Test',
        'slug' => 'valid-slug-test',
        'content' => 'Content',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->get('/article/valid-slug-test')->assertOk();
});

it('rejects invalid characters in category slugs', function () {
    $this->get('/category/test%20invalid')->assertNotFound();
});

it('rejects invalid characters in page slugs', function () {
    $this->get('/page/test%3Cscript%3E')->assertNotFound();
});

it('strips XSS from article content in response', function () {
    Article::create([
        'title' => 'XSS Article',
        'slug' => 'xss-article',
        'content' => '<p>Safe</p><script>document.cookie</script>',
        'is_published' => true,
        'published_at' => now(),
    ]);

    $this->get('/article/xss-article')
        ->assertOk()
        ->assertDontSee('<script>', false)
        ->assertSee('Safe');
});

it('strips XSS from page content in response', function () {
    Page::create([
        'title' => 'XSS Page',
        'slug' => 'xss-page',
        'content' => '<p>Safe</p><script>alert(1)</script>',
        'is_published' => true,
    ]);

    $this->get('/page/xss-page')
        ->assertOk()
        ->assertDontSee('<script>', false);
});
