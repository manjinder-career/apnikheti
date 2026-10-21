<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeedPhoto extends Model
{
    use HasFactory;

    protected $fillable = [
        'seed_id',
        'photo_path',
        'caption',
    ];

    public function seed(): BelongsTo
    {
        return $this->belongsTo(Seed::class);
    }
}
