<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\NonLeafImageException;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreDiagnosisRequest;
use App\Models\Diagnosis;
use App\Models\Disease;
use App\Services\AiPredictionService;
use App\Support\ApiTokenAuthenticator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DiagnosisController extends Controller
{
    public function index(Request $request, ApiTokenAuthenticator $auth): JsonResponse
    {
        $user = $auth->userFromRequest($request);

        abort_if(! $user, 401, 'Unauthenticated.');

        return response()->json([
            'diagnoses' => $user->diagnoses()
                ->with(['crop', 'disease.treatments'])
                ->latest()
                ->get()
                ->map(fn (Diagnosis $diagnosis) => $this->diagnosisPayload($diagnosis, $user->language_preference))
                ->values(),
        ]);
    }

    public function store(
        StoreDiagnosisRequest $request,
        ApiTokenAuthenticator $auth,
        AiPredictionService $predictionService
    ): JsonResponse
    {
        $user = $auth->userFromRequest($request);

        abort_if(! $user, 401, 'Unauthenticated.');

        $data = $request->validated();

        $imagePath = $request->file('image')->store('diagnoses', 'public');
        try {
            $prediction = $predictionService->predict($imagePath, $data['crop_id'] ?? null);
        } catch (NonLeafImageException $exception) {
            Storage::disk('public')->delete($imagePath);

            return response()->json([
                'message' => $exception->getMessage(),
                'code' => 'crop_leaf_not_detected',
            ], 422);
        }
        $disease = Disease::query()
            ->where('class_label', $prediction->label)
            ->when($data['crop_id'] ?? null, fn ($query, $cropId) => $query->where('crop_id', $cropId))
            ->with('treatments')
            ->first();

        $diagnosis = Diagnosis::query()->create([
            'user_id' => $user->id,
            'crop_id' => $disease?->crop_id ?? ($data['crop_id'] ?? null),
            'disease_id' => $disease?->id,
            'image_path' => $imagePath,
            'predicted_label' => $prediction->label,
            'confidence' => $prediction->confidence,
            'recommendation_snapshot' => $disease
                ? $this->recommendationSnapshot($disease, $user->language_preference)
                : 'No matching disease is available yet. Please consult an agricultural extension officer.',
            'status' => 'completed',
        ]);

        return response()->json([
            'message' => $prediction->usedFallback
                ? 'Diagnosis completed using the temporary prediction fallback.'
                : 'Diagnosis completed.',
            'diagnosis' => $this->diagnosisPayload(
                $diagnosis->load(['crop', 'disease.treatments']),
                $user->language_preference
            ),
        ], 201);
    }

    public function show(Request $request, Diagnosis $diagnosis, ApiTokenAuthenticator $auth): JsonResponse
    {
        $user = $auth->userFromRequest($request);

        abort_if(! $user, 401, 'Unauthenticated.');
        abort_if($diagnosis->user_id !== $user->id && $user->role !== 'admin', 403, 'Forbidden.');

        return response()->json([
            'diagnosis' => $this->diagnosisPayload(
                $diagnosis->load(['crop', 'disease.treatments']),
                $user->language_preference
            ),
        ]);
    }

    private function diagnosisPayload(Diagnosis $diagnosis, string $language): array
    {
        $payload = $diagnosis->toArray();
        unset($payload['image_path']);

        $payload['image_url'] = $diagnosis->image_path
            ? Storage::disk('public')->url($diagnosis->image_path)
            : null;

        if ($diagnosis->relationLoaded('crop') && $diagnosis->crop) {
            $payload['crop'] = $this->localizedCrop($diagnosis->crop, $language);
        }

        if ($diagnosis->relationLoaded('disease') && $diagnosis->disease) {
            $payload['disease'] = $this->localizedDisease($diagnosis->disease, $language);
        }

        return $payload;
    }

    private function recommendationSnapshot(Disease $disease, string $language): string
    {
        $treatments = $disease->treatments
            ->map(fn ($treatment) => $this->localizedValue($treatment->title, $treatment->title_sn, $language).': '.$this->localizedValue($treatment->instructions, $treatment->instructions_sn, $language))
            ->implode("\n");

        $symptomsLabel = $language === 'sn' ? 'Zviratidzo' : 'Symptoms';
        $preventionLabel = $language === 'sn' ? 'Kudzivirira' : 'Prevention';
        $treatmentsLabel = $language === 'sn' ? 'Mishonga nematanho' : 'Treatments';
        $symptoms = $this->localizedValue($disease->symptoms, $disease->symptoms_sn, $language);
        $prevention = $this->localizedValue($disease->prevention, $disease->prevention_sn, $language);

        return trim("{$symptomsLabel}: {$symptoms}\n{$preventionLabel}: {$prevention}\n{$treatmentsLabel}:\n{$treatments}");
    }

    private function localizedCrop($crop, string $language): array
    {
        $payload = $crop->toArray();
        $payload['canonical_name'] = $crop->name;
        $payload['name'] = $this->localizedValue($crop->name, $crop->name_sn, $language);
        $payload['description'] = $this->localizedValue($crop->description, $crop->description_sn, $language);

        return $payload;
    }

    private function localizedDisease(Disease $disease, string $language): array
    {
        $payload = $disease->toArray();
        $payload['name'] = $this->localizedValue($disease->name, $disease->name_sn, $language);
        $payload['description'] = $this->localizedValue($disease->description, $disease->description_sn, $language);
        $payload['symptoms'] = $this->localizedValue($disease->symptoms, $disease->symptoms_sn, $language);
        $payload['prevention'] = $this->localizedValue($disease->prevention, $disease->prevention_sn, $language);

        if ($disease->relationLoaded('treatments')) {
            $payload['treatments'] = $disease->treatments
                ->map(fn ($treatment) => [
                    ...$treatment->toArray(),
                    'title' => $this->localizedValue($treatment->title, $treatment->title_sn, $language),
                    'instructions' => $this->localizedValue($treatment->instructions, $treatment->instructions_sn, $language),
                ])
                ->values()
                ->all();
        }

        return $payload;
    }

    private function localizedValue(?string $english, ?string $shona, string $language): ?string
    {
        return $language === 'sn' && filled($shona) ? $shona : $english;
    }
}
