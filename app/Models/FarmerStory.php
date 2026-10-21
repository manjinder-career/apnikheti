<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FarmerStory extends Model
{
    use HasFactory;

    protected $fillable = [
        'farmer_name',
        'village',
        'district',
        'crop_name',
        'farming_practice',
        'description',
        'photo_path',
        'status',
        'display_consent',
        'contact_info_private',
    ];

    protected $casts = [
        'display_consent' => 'boolean',
    ];

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }
}
