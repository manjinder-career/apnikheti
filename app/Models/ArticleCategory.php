<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ArticleCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'name_en',
        'name_pa',
        'description',
    ];

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
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
}
