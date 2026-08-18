<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

#[Hidden(['image_path'])]
class Diagnosis extends Model
{
    protected $appends = [
        'image_url',
    ];

    protected $fillable = [
        'user_id',
        'crop_id',
        'disease_id',
        'image_path',
        'predicted_label',
        'confidence',
        'recommendation_snapshot',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'confidence' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }

    public function getImageUrlAttribute(): string
    {
        return Storage::disk('public')->url($this->image_path);
    }
}
