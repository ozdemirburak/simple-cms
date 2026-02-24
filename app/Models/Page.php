<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Stevebauman\Purify\Facades\Purify;

class Page extends Model
{
    protected $fillable = [
        'parent_id',
        'title',
        'slug',
        'excerpt',
        'content',
        'is_published',
        'sort_order',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected static function booted(): void
    {
        static::creating(function (Page $page) {
            if (empty($page->slug)) {
                $baseSlug = Str::slug($page->title);
                $slug = $baseSlug;
                $count = 1;
                while (Page::where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.++$count;
                }
                $page->slug = $slug;
            }
        });

        static::saving(function (Page $page) {
            if ($page->isDirty('content') && $page->content) {
                $page->content = Purify::clean($page->content);
            }
        });

        static::saved(fn () => cache()->forget('nav_pages'));
        static::deleted(fn () => cache()->forget('nav_pages'));
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Page::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Page::class, 'parent_id')->orderBy('sort_order');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function scopeRoots($query)
    {
        return $query->whereNull('parent_id');
    }
}
