<?php

namespace Tests\Unit;

use App\Models\Crop;
use App\Models\Diagnosis;
use App\Models\Disease;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DomainModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_crop_disease_treatment_and_diagnosis_relationships_are_wired(): void
    {
        $user = User::factory()->create();
        $crop = Crop::query()->create(['name' => 'Tomato']);
        $disease = Disease::query()->create([
            'crop_id' => $crop->id,
            'name' => 'Early Blight',
            'class_label' => 'tomato_early_blight',
        ]);
        $treatment = Treatment::query()->create([
            'disease_id' => $disease->id,
            'title' => 'Remove leaves',
            'instructions' => 'Remove infected leaves.',
        ]);
        $diagnosis = Diagnosis::query()->create([
            'user_id' => $user->id,
            'crop_id' => $crop->id,
            'disease_id' => $disease->id,
            'image_path' => 'diagnoses/test.png',
            'predicted_label' => 'tomato_early_blight',
            'confidence' => 88.5,
            'status' => 'completed',
        ]);

        $this->assertTrue($crop->diseases->contains($disease));
        $this->assertTrue($disease->treatments->contains($treatment));
        $this->assertTrue($user->diagnoses->contains($diagnosis));
        $this->assertSame('Tomato', $diagnosis->crop->name);
    }
}
