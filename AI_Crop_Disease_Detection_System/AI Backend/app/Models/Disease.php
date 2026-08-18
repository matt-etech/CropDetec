<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Disease extends Model
{
    protected $fillable = [
        'crop_id',
        'name',
        'name_sn',
        'class_label',
        'description',
        'description_sn',
        'symptoms',
        'symptoms_sn',
        'prevention',
        'prevention_sn',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function crop(): BelongsTo
    {
        return $this->belongsTo(Crop::class);
    }

    public function treatments(): HasMany
    {
        return $this->hasMany(Treatment::class);
    }

    public function diagnoses(): HasMany
    {
        return $this->hasMany(Diagnosis::class);
    }
}
