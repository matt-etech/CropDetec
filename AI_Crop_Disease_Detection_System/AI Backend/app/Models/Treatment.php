<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Treatment extends Model
{
    protected $fillable = [
        'disease_id',
        'title',
        'title_sn',
        'instructions',
        'instructions_sn',
        'type',
    ];

    public function disease(): BelongsTo
    {
        return $this->belongsTo(Disease::class);
    }
}
