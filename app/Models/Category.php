<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::creating(function (Category $category) {
            if (empty($category->slug)) {
                $baseSlug = Str::slug($category->title);
                $slug = $baseSlug;
                $count = 1;
                while (Category::where('slug', $slug)->exists()) {
                    $slug = $baseSlug.'-'.++$count;
                }
                $category->slug = $slug;
            }
        });
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }
}
