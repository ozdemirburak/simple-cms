<?php

use App\Models\Page;

it('auto-generates slug from title', function () {
    $page = Page::create(['title' => 'About Us', 'content' => 'Content', 'is_published' => true]);

    expect($page->slug)->toBe('about-us');
});

it('generates unique slug when title duplicates', function () {
    Page::create(['title' => 'About Us', 'is_published' => true]);
    $second = Page::create(['title' => 'About Us', 'is_published' => true]);

    expect($second->slug)->toBe('about-us-2');
});

it('filters published pages with scope', function () {
    Page::create(['title' => 'Published', 'slug' => 'published', 'is_published' => true]);
    Page::create(['title' => 'Draft', 'slug' => 'draft', 'is_published' => false]);

    expect(Page::published()->count())->toBe(1);
});

it('can have a parent page', function () {
    $parent = Page::create(['title' => 'Parent', 'slug' => 'parent', 'is_published' => true]);
    $child = Page::create(['title' => 'Child', 'slug' => 'child', 'parent_id' => $parent->id, 'is_published' => true]);

    expect($child->parent)
        ->toBeInstanceOf(Page::class)
        ->id->toBe($parent->id);
});

it('can have children pages', function () {
    $parent = Page::create(['title' => 'Parent', 'slug' => 'parent', 'is_published' => true]);
    Page::create(['title' => 'Child 1', 'slug' => 'child-1', 'parent_id' => $parent->id, 'is_published' => true]);
    Page::create(['title' => 'Child 2', 'slug' => 'child-2', 'parent_id' => $parent->id, 'is_published' => true]);

    expect($parent->children)->toHaveCount(2);
});

it('returns only top-level pages with roots scope', function () {
    $parent = Page::create(['title' => 'Root', 'slug' => 'root', 'is_published' => true]);
    Page::create(['title' => 'Child', 'slug' => 'child', 'parent_id' => $parent->id, 'is_published' => true]);

    expect(Page::roots()->count())->toBe(1);
});

it('sanitizes content on save', function () {
    $page = Page::create([
        'title' => 'XSS Page',
        'slug' => 'xss-page',
        'content' => '<p>Safe</p><script>alert("xss")</script>',
        'is_published' => true,
    ]);

    expect($page->content)
        ->toContain('<p>Safe</p>')
        ->not->toContain('<script>');
});

it('strips iframes from content', function () {
    $page = Page::create([
        'title' => 'Iframe Test',
        'slug' => 'iframe-test',
        'content' => '<p>Content</p><iframe src="https://evil.com"></iframe>',
        'is_published' => true,
    ]);

    expect($page->content)
        ->toContain('<p>Content</p>')
        ->not->toContain('<iframe');
});
