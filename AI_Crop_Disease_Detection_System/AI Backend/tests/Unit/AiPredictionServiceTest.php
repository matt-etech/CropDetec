<?php

namespace Tests\Unit;

use App\Models\Crop;
use App\Models\Disease;
use App\Services\AiPredictionService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AiPredictionServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_uses_fallback_prediction_when_service_is_not_configured(): void
    {
        $crop = Crop::query()->create(['name' => 'Tomato']);
        Disease::query()->create([
            'crop_id' => $crop->id,
            'name' => 'Early Blight',
            'class_label' => 'tomato_early_blight',
        ]);

        config(['services.ai.url' => null]);

        $prediction = app(AiPredictionService::class)->predict('diagnoses/fake.png', $crop->id);

        $this->assertTrue($prediction->usedFallback);
        $this->assertSame('tomato_early_blight', $prediction->label);
        $this->assertSame(88.5, $prediction->confidence);
    }

    public function test_it_normalizes_fractional_service_confidence(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('diagnoses/fake.png', 'image-bytes');
        Http::fake([
            'https://ai-service.test/predict' => Http::response([
                'predicted_label' => 'maize_leaf_blight',
                'confidence' => 0.8123,
            ]),
        ]);
        config(['services.ai.url' => 'https://ai-service.test']);

        $prediction = app(AiPredictionService::class)->predict('diagnoses/fake.png', null);

        $this->assertFalse($prediction->usedFallback);
        $this->assertSame('maize_leaf_blight', $prediction->label);
        $this->assertSame(81.23, $prediction->confidence);

        Http::assertSent(fn ($request) => ! array_key_exists('crop_id', $request->data()));
    }
}
