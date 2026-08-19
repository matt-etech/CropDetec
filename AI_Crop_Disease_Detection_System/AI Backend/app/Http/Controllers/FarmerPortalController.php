<?php

namespace App\Http\Controllers;

use App\Support\ZimbabwePhone;
use App\Models\Crop;
use App\Models\Diagnosis;
use App\Models\Disease;
use App\Models\User;
use App\Services\AiPredictionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class FarmerPortalController extends Controller
{
    public function createDiagnosis(Request $request): View|RedirectResponse
    {
        $user = $this->webUser($request);

        if (! $user) {
            return redirect('/login');
        }

        return view('farmer.diagnose', [
            'user' => $user,
            'crops' => Crop::query()->orderBy('name')->get(),
        ]);
    }

    public function storeDiagnosis(
        Request $request,
        AiPredictionService $predictionService
    ): RedirectResponse {
        $user = $this->webUser($request);

        if (! $user) {
            return redirect('/login');
        }

        $data = $request->validate([
            'image' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'crop_id' => ['nullable', 'exists:crops,id'],
        ]);

        $imagePath = $request->file('image')->store('diagnoses', 'public');
        $prediction = $predictionService->predict($imagePath, $data['crop_id'] ?? null);
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

        return redirect('/diagnoses/'.$diagnosis->id);
    }

    public function history(Request $request): View|RedirectResponse
    {
        $user = $this->webUser($request);

        if (! $user) {
            return redirect('/login');
        }

        $query = $user->diagnoses()
            ->with(['crop', 'disease'])
            ->latest();

        if ($request->filled('crop_id')) {
            $query->where('crop_id', $request->integer('crop_id'));
        }

        if ($request->filled('disease_id')) {
            $query->where('disease_id', $request->integer('disease_id'));
        }

        if ($request->date === 'today') {
            $query->whereDate('created_at', now()->toDateString());
        }

        if ($request->date === 'week') {
            $query->where('created_at', '>=', now()->subDays(7));
        }

        return view('farmer.history', [
            'user' => $user,
            'diagnoses' => $query->get(),
            'crops' => Crop::query()->orderBy('name')->get(),
            'diseases' => Disease::query()->orderBy('name')->get(),
            'filters' => $request->only(['crop_id', 'disease_id', 'date']),
        ]);
    }

    public function showDiagnosis(Request $request, Diagnosis $diagnosis): View|RedirectResponse
    {
        $user = $this->webUser($request);

        if (! $user) {
            return redirect('/login');
        }

        abort_if($diagnosis->user_id !== $user->id, 403);

        return view('farmer.diagnosis-show', [
            'user' => $user,
            'diagnosis' => $diagnosis->load(['crop', 'disease.treatments']),
            'imageUrl' => Storage::disk('public')->url($diagnosis->image_path),
        ]);
    }

    public function cropLibrary(Request $request): View|RedirectResponse
    {
        $user = $this->webUser($request);

        if (! $user) {
            return redirect('/login');
        }

        return view('farmer.crops', [
            'user' => $user,
            'crops' => Crop::query()->with('diseases.treatments')->orderBy('name')->get(),
        ]);
    }

    public function profile(Request $request): View|RedirectResponse
    {
        $user = $this->webUser($request);

        if (! $user) {
            return redirect('/login');
        }

        return view('farmer.profile', ['user' => $user]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $this->webUser($request);

        if (! $user) {
            return redirect('/login');
        }

        $request->merge(['phone' => ZimbabwePhone::normalize($request->input('phone'))]);

        $user->update($request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ZimbabwePhone::rules(),
            'language_preference' => ['required', Rule::in(['en', 'sn'])],
        ]));

        return redirect('/profile')->with('status_key', 'profile_updated');
    }

    private function webUser(Request $request): ?User
    {
        $userId = $request->session()->get('web_user_id');

        if (! $userId) {
            return null;
        }

        return User::query()->find($userId);
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

    private function localizedValue(?string $english, ?string $shona, string $language): ?string
    {
        return $language === 'sn' && filled($shona) ? $shona : $english;
    }
}
