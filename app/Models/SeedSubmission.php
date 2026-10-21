<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeedSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'contributor_name',
        'village',
        'district',
        'state',
        'phone',
        'email',
        'seed_name_pa',
        'seed_name_en',
        'crop_category_name',
        'local_names',
        'description',
        'traditional_uses',
        'growing_season',
        'preservation_method',
        'duration_known',
        'photo_path',
        'contact_consent',
        'display_consent',
        'status',
        'review_notes',
        'reviewer_id',
    ];

    protected $casts = [
        'contact_consent' => 'boolean',
        'display_consent' => 'boolean',
    ];

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }
}
