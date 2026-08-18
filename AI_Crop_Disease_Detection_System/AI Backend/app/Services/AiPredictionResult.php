<?php

namespace App\Services;

class AiPredictionResult
{
    public function __construct(
        public readonly string $label,
        public readonly float $confidence,
        public readonly bool $usedFallback = false,
    ) {
    }
}
