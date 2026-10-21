<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'article_category_id',
        'slug',
        'title_en',
        'title_pa',
        'excerpt_en',
        'excerpt_pa',
        'content_en',
        'content_pa',
        'author_name',
        'featured_image',
        'is_featured',
        'is_published',
        'published_at',
        'views_count',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
        'published_at' => 'datetime',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id');
    }

    public function getTitleAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'pa' && !empty($this->title_pa)) {
            return $this->title_pa;
        }
        if ($locale === 'hi' && !empty($this->title_hi)) {
            return $this->title_hi;
        }
        return $this->title_en;
    }

    public function getExcerptAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'pa' && !empty($this->excerpt_pa)) {
            return $this->excerpt_pa;
        }
        if ($locale === 'hi' && !empty($this->excerpt_hi)) {
            return $this->excerpt_hi;
        }
        return $this->excerpt_en;
    }

    public function getContentAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'pa' && !empty($this->content_pa)) {
            return $this->content_pa;
        }
        if ($locale === 'hi' && !empty($this->content_hi)) {
            return $this->content_hi;
        }
        return $this->content_en;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
