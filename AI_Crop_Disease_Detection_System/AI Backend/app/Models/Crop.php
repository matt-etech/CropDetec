<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Crop extends Model
{
    protected $fillable = [
        'name',
        'name_sn',
        'scientific_name',
        'description',
        'description_sn',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function diseases(): HasMany
    {
        return $this->hasMany(Disease::class);
    }
}
