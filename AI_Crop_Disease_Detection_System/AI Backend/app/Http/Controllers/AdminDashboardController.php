<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use App\Models\Crop;
use App\Models\Diagnosis;
use App\Models\Disease;
use App\Models\Treatment;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    public function index(Request $request): View
    {
        $admin = $this->adminFromRequest($request);

        return view('admin.dashboard', [
            'admin' => $admin,
            'token' => $this->plainToken($request),
            'stats' => [
                'users' => User::count(),
                'crops' => Crop::count(),
                'diseases' => Disease::count(),
                'treatments' => Treatment::count(),
                'diagnoses' => Diagnosis::count(),
                'lowConfidence' => Diagnosis::query()->where('confidence', '<', 60)->count(),
            ],
            'users' => User::query()->latest()->limit(25)->get(),
            'crops' => Crop::query()->with('diseases')->orderBy('name')->get(),
            'diseases' => Disease::query()->with('crop')->orderBy('name')->get(),
            'treatments' => Treatment::query()->with('disease.crop')->latest()->limit(50)->get(),
            'diagnoses' => Diagnosis::query()
                ->with(['user:id,name,email', 'crop', 'disease'])
                ->latest()
                ->limit(50)
                ->get(),
        ]);
    }

    public function storeCrop(Request $request): RedirectResponse
    {
        $this->adminFromRequest($request);

        Crop::query()->create($request->validate([
            'name' => ['required', 'string', 'max:120'],
            'name_sn' => ['nullable', 'string', 'max:120'],
            'scientific_name' => ['nullable', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:1000'],
            'description_sn' => ['nullable', 'string', 'max:1000'],
        ]));

        return $this->backToAdmin($request);
    }

    public function storeDisease(Request $request): RedirectResponse
    {
        $this->adminFromRequest($request);

        Disease::query()->create($request->validate([
            'crop_id' => ['required', 'exists:crops,id'],
            'name' => ['required', 'string', 'max:160'],
            'name_sn' => ['nullable', 'string', 'max:160'],
            'class_label' => ['required', 'string', 'max:160', 'unique:diseases,class_label'],
            'description' => ['nullable', 'string', 'max:1000'],
            'description_sn' => ['nullable', 'string', 'max:1000'],
            'symptoms' => ['nullable', 'string', 'max:2000'],
            'symptoms_sn' => ['nullable', 'string', 'max:2000'],
            'prevention' => ['nullable', 'string', 'max:2000'],
            'prevention_sn' => ['nullable', 'string', 'max:2000'],
        ]));

        return $this->backToAdmin($request);
    }

    public function storeTreatment(Request $request): RedirectResponse
    {
        $this->adminFromRequest($request);

        Treatment::query()->create($request->validate([
            'disease_id' => ['required', 'exists:diseases,id'],
            'title' => ['required', 'string', 'max:160'],
            'title_sn' => ['nullable', 'string', 'max:160'],
            'instructions' => ['required', 'string', 'max:2000'],
            'instructions_sn' => ['nullable', 'string', 'max:2000'],
            'type' => ['nullable', 'string', 'max:80'],
        ]));

        return $this->backToAdmin($request);
    }

    public function updateUserRole(Request $request, User $user): RedirectResponse
    {
        $this->adminFromRequest($request);

        $data = $request->validate([
            'role' => ['required', 'in:farmer,admin'],
        ]);

        $user->update($data);

        return $this->backToAdmin($request);
    }

    private function adminFromRequest(Request $request): User
    {
        $plainToken = $this->plainToken($request);

        abort_if(! $plainToken, 401, 'Admin token is required.');

        $token = ApiToken::query()
            ->where('token_hash', hash('sha256', $plainToken))
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        abort_if(! $token, 401, 'Invalid admin token.');

        $user = $token->user;

        abort_if($user->role !== 'admin', 403, 'Administrator access is required.');

        return $user;
    }

    private function plainToken(Request $request): ?string
    {
        return $request->bearerToken() ?? $request->query('token') ?? $request->input('token');
    }

    private function backToAdmin(Request $request): RedirectResponse
    {
        return redirect('/admin?token='.urlencode((string) $this->plainToken($request)));
    }
}
