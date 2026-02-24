<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Stevebauman\Purify\Facades\Purify;

class Article extends Model
{
    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'featured_image',
        'is_published',
        'published_at',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Article $article) {
            if (empty($article->slug)) {
                $baseSlug = Str::slug($article->title);
                $slug = $baseSlug;
                $count = 1;
                while (Article::where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.++$count;
                }
                $article->slug = $slug;
            }
        });

        static::saving(function (Article $article) {
            if ($article->isDirty('content') && $article->content) {
                $article->content = Purify::clean($article->content);
            }
        });
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function views(): HasMany
    {
        return $this->hasMany(ArticleView::class);
    }

    public function recordView(?string $ipAddress = null, ?string $userAgent = null, ?string $referer = null): ArticleView
    {
        return $this->views()->create([
            'ip_address' => $ipAddress,
            'user_agent' => $userAgent ? mb_substr($userAgent, 0, 255) : null,
            'referer' => $referer ? mb_substr($referer, 0, 255) : null,
            'viewed_at' => now(),
        ]);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

}
