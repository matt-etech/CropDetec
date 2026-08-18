<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Crop;
use App\Models\Disease;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CropController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $language = $this->languageFromRequest($request);

        return response()->json([
            'crops' => Crop::query()
                ->where('is_active', true)
                ->with([
                    'diseases' => fn ($query) => $query
                        ->where('is_active', true)
                        ->with('treatments'),
                ])
                ->orderBy('name')
                ->get()
                ->map(fn (Crop $crop) => $this->localizedCrop($crop, $language))
                ->values(),
        ]);
    }

    public function diseases(Request $request): JsonResponse
    {
        $language = $this->languageFromRequest($request);

        $query = Disease::query()
            ->where('is_active', true)
            ->with(['crop', 'treatments'])
            ->orderBy('name');

        if ($request->filled('crop_id')) {
            $query->where('crop_id', $request->integer('crop_id'));
        }

        return response()->json([
            'diseases' => $query->get()
                ->map(fn (Disease $disease) => $this->localizedDisease($disease, $language))
                ->values(),
        ]);
    }

    private function languageFromRequest(Request $request): string
    {
        return str_starts_with(strtolower($request->header('Accept-Language', '')), 'sn')
            ? 'sn'
            : 'en';
    }

    private function localizedCrop(Crop $crop, string $language): array
    {
        $payload = $crop->toArray();
        $payload['name'] = $this->localizedValue($crop->name, $crop->name_sn, $language);
        $payload['description'] = $this->localizedValue($crop->description, $crop->description_sn, $language);

        if ($crop->relationLoaded('diseases')) {
            $payload['diseases'] = $crop->diseases
                ->map(fn (Disease $disease) => $this->localizedDisease($disease, $language))
                ->values()
                ->all();
        }

        return $payload;
    }

    private function localizedDisease(Disease $disease, string $language): array
    {
        $payload = $disease->toArray();
        $payload['name'] = $this->localizedValue($disease->name, $disease->name_sn, $language);
        $payload['description'] = $this->localizedValue($disease->description, $disease->description_sn, $language);
        $payload['symptoms'] = $this->localizedValue($disease->symptoms, $disease->symptoms_sn, $language);
        $payload['prevention'] = $this->localizedValue($disease->prevention, $disease->prevention_sn, $language);

        if ($disease->relationLoaded('crop') && $disease->crop) {
            $payload['crop'] = $this->localizedCrop($disease->crop, $language);
        }

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
