<?php

namespace App\Services;

use App\Exceptions\NonLeafImageException;
use App\Models\Disease;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Throwable;

class AiPredictionService
{
    public function predict(string $imagePath, ?int $cropId): AiPredictionResult
    {
        $serviceUrl = config('services.ai.url');

        if (! $serviceUrl) {
            return $this->fallbackPrediction($cropId);
        }

        try {
            $formData = $cropId === null ? [] : ['crop_id' => $cropId];

            $response = Http::timeout((int) config('services.ai.timeout', 10))
                ->attach(
                    'image',
                    Storage::disk('public')->get($imagePath),
                    basename($imagePath)
                )
                ->post(rtrim($serviceUrl, '/').'/predict', $formData);

            if ($response->status() === 422) {
                $detail = $response->json('detail');

                throw new NonLeafImageException(
                    is_string($detail) && $detail !== ''
                        ? $detail
                        : 'No crop leaf was detected. Retake a clear photo with one leaf filling most of the frame.'
                );
            }

            if (! $response->successful()) {
                return $this->fallbackPrediction($cropId);
            }

            $label = $response->json('predicted_label')
                ?? $response->json('label')
                ?? $response->json('class_label');
            $confidence = $response->json('confidence');

            if (! is_string($label) || ! is_numeric($confidence)) {
                return $this->fallbackPrediction($cropId);
            }

            return new AiPredictionResult(
                label: $label,
                confidence: $this->normalizeConfidence((float) $confidence),
            );
        } catch (NonLeafImageException $exception) {
            throw $exception;
        } catch (Throwable) {
            return $this->fallbackPrediction($cropId);
        }
    }

    private function fallbackPrediction(?int $cropId): AiPredictionResult
    {
        $disease = Disease::query()
            ->when($cropId, fn ($query) => $query->where('crop_id', $cropId))
            ->first();

        return new AiPredictionResult(
            label: $disease?->class_label ?? 'unknown',
            confidence: $disease ? 88.50 : 0,
            usedFallback: true,
        );
    }

    private function normalizeConfidence(float $confidence): float
    {
        if ($confidence <= 1) {
            return round($confidence * 100, 2);
        }

        return round($confidence, 2);
    }
}
