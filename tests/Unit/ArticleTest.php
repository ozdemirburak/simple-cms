<?php

use App\Models\Article;
use App\Models\ArticleView;
use App\Models\Category;

describe('model', function () {
    it('auto-generates slug from title', function () {
        $article = Article::create([
            'title' => 'My Test Article',
            'content' => 'Content',
            'is_published' => false,
        ]);

        expect($article->slug)->toBe('my-test-article');
    });

    it('belongs to a category', function () {
        $category = Category::create(['title' => 'Tech', 'slug' => 'tech', 'is_active' => true]);

        $article = Article::create([
            'title' => 'Article',
            'slug' => 'article',
            'content' => 'Content',
            'category_id' => $category->id,
            'is_published' => false,
        ]);

        expect($article->category)
            ->toBeInstanceOf(Category::class)
            ->id->toBe($category->id);
    });

    it('can have null category', function () {
        $article = Article::create([
            'title' => 'No Category',
            'slug' => 'no-category',
            'content' => 'Content',
            'is_published' => false,
        ]);

        expect($article->category)->toBeNull();
    });

    it('filters published articles with scope', function () {
        Article::create(['title' => 'Published', 'slug' => 'published', 'content' => 'C', 'is_published' => true, 'published_at' => now()->subDay()]);
        Article::create(['title' => 'Draft', 'slug' => 'draft', 'content' => 'C', 'is_published' => false]);
        Article::create(['title' => 'Future', 'slug' => 'future', 'content' => 'C', 'is_published' => true, 'published_at' => now()->addDay()]);

        expect(Article::published()->count())->toBe(1);
    });

    it('defaults view count to zero', function () {
        $article = Article::create(['title' => 'New', 'slug' => 'new', 'content' => 'C', 'is_published' => false]);

        expect($article->views()->count())->toBe(0);
    });

    it('generates unique slug when title duplicates', function () {
        Article::create(['title' => 'Duplicate', 'content' => 'C', 'is_published' => false]);
        $second = Article::create(['title' => 'Duplicate', 'content' => 'C', 'is_published' => false]);

        expect($second->slug)->toBe('duplicate-2');
    });
});

describe('views', function () {
    beforeEach(function () {
        $this->article = Article::create(['title' => 'Test', 'slug' => 'test', 'content' => 'C', 'is_published' => true, 'published_at' => now()]);
    });

    it('records a view', function () {
        $view = $this->article->recordView('127.0.0.1', 'Test Agent', 'https://google.com');

        expect($view)
            ->toBeInstanceOf(ArticleView::class)
            ->article_id->toBe($this->article->id)
            ->ip_address->toBe('127.0.0.1')
            ->user_agent->toBe('Test Agent')
            ->referer->toBe('https://google.com')
            ->viewed_at->not->toBeNull();
    });

    it('has many views', function () {
        $this->article->recordView('127.0.0.1');
        $this->article->recordView('192.168.1.1');

        expect($this->article->views)->toHaveCount(2);
    });

    it('allows null optional fields', function () {
        $view = $this->article->recordView(null, null, null);

        expect($view->ip_address)->toBeNull()
            ->and($view->user_agent)->toBeNull()
            ->and($view->referer)->toBeNull();
    });

    it('casts viewed_at to datetime', function () {
        $view = ArticleView::create(['article_id' => $this->article->id, 'viewed_at' => '2025-01-15 10:30:00']);

        expect($view->viewed_at)->toBeInstanceOf(\Illuminate\Support\Carbon::class);
    });

    it('deletes views when article is deleted', function () {
        $this->article->recordView('127.0.0.1');
        $this->article->recordView('192.168.1.1');

        expect(ArticleView::count())->toBe(2);

        $this->article->delete();

        expect(ArticleView::count())->toBe(0);
    });

    it('truncates user agent to 255 chars', function () {
        $view = $this->article->recordView('127.0.0.1', str_repeat('A', 500), null);

        expect(mb_strlen($view->user_agent))->toBe(255);
    });

    it('truncates referer to 255 chars', function () {
        $view = $this->article->recordView('127.0.0.1', null, 'https://example.com/' . str_repeat('a', 500));

        expect(mb_strlen($view->referer))->toBe(255);
    });
});

describe('content sanitization', function () {
    it('strips script tags', function () {
        $article = Article::create([
            'title' => 'XSS Test',
            'slug' => 'xss-test',
            'content' => '<p>Safe</p><script>alert("xss")</script>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        expect($article->content)
            ->toContain('<p>Safe</p>')
            ->not->toContain('<script>');
    });

    it('preserves safe HTML', function () {
        $article = Article::create([
            'title' => 'Safe HTML',
            'slug' => 'safe-html',
            'content' => '<h2>Title</h2><p>Text with <strong>bold</strong> and <em>italic</em>.</p><ul><li>Item</li></ul>',
            'is_published' => true,
            'published_at' => now(),
        ]);

        expect($article->content)
            ->toContain('<h2>Title</h2>')
            ->toContain('<strong>bold</strong>')
            ->toContain('<em>italic</em>');
    });

    it('strips event handlers', function () {
        $article = Article::create([
            'title' => 'Event Handler',
            'slug' => 'event-handler',
            'content' => '<p onmouseover="alert(1)">Hover</p><img src="x" onerror="alert(1)">',
            'is_published' => true,
            'published_at' => now(),
        ]);

        expect($article->content)
            ->not->toContain('onmouseover')
            ->not->toContain('onerror');
    });

    it('does not sanitize null content', function () {
        $article = Article::create([
            'title' => 'No Content',
            'slug' => 'no-content',
            'content' => null,
            'is_published' => true,
            'published_at' => now(),
        ]);

        expect($article->content)->toBeNull();
    });
});
