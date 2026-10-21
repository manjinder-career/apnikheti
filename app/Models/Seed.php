<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Seed extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'seed_category_id',
        'slug',
        'name_pa',
        'name_en',
        'local_names',
        'scientific_name',
        'description_en',
        'description_pa',
        'region_of_origin',
        'growing_season',
        'sowing_period',
        'soil_preferences',
        'water_requirements',
        'crop_duration',
        'traditional_uses',
        'culinary_uses',
        'farmer_observations',
        'preservation_methods',
        'featured_image',
        'video_links',
        'is_featured',
        'is_verified',
        'publication_status',
        'contributor_name',
        'user_id',
    ];

    protected $casts = [
        'video_links' => 'array',
        'is_featured' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(SeedCategory::class, 'seed_category_id');
    }

    public function photos(): HasMany
    {
        return $this->hasMany(SeedPhoto::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        if ($locale === 'pa' && !empty($this->name_pa)) {
            return $this->name_pa;
        }
        if ($locale === 'hi' && !empty($this->name_hi)) {
            return $this->name_hi;
        }
        return $this->name_en;
    }

    public function getDescriptionAttribute(): ?string
    {
        $locale = app()->getLocale();
        if ($locale === 'pa' && !empty($this->description_pa)) {
            return $this->description_pa;
        }
        if ($locale === 'hi' && !empty($this->description_hi)) {
            return $this->description_hi;
        }
        return $this->description_en;
    }

    public function scopePublished($query)
    {
        return $query->where('publication_status', 'published');
    }

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }
}
