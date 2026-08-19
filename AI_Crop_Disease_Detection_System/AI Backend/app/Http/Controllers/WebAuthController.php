<?php

namespace App\Http\Controllers;

use App\Models\ApiToken;
use App\Models\User;
use App\Support\ZimbabwePhone;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class WebAuthController extends Controller
{
    public function showRegister(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->merge(['phone' => ZimbabwePhone::normalize($request->input('phone'))]);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone' => ZimbabwePhone::rules(),
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'language_preference' => ['required', Rule::in(['en', 'sn'])],
        ]);

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'language_preference' => $data['language_preference'],
            'role' => 'farmer',
        ]);

        $this->startWebSession($request, $user);

        return redirect('/dashboard');
    }

    public function showLogin(): View
    {
        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $user = User::query()->where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return back()
                ->withErrors(['email' => 'The provided credentials are incorrect.'])
                ->onlyInput('email');
        }

        $this->startWebSession($request, $user);

        return redirect('/dashboard');
    }

    public function dashboard(Request $request): View|RedirectResponse
    {
        $user = $this->webUser($request);

        if (! $user) {
            return redirect('/login');
        }

        return view('farmer.dashboard', [
            'user' => $user,
            'diagnoses' => $user->diagnoses()
                ->with(['crop', 'disease'])
                ->latest()
                ->limit(10)
                ->get(),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $token = $request->session()->pull('web_api_token');
        $request->session()->forget('web_user_id');

        if ($token) {
            ApiToken::query()
                ->where('token_hash', hash('sha256', $token))
                ->delete();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    private function startWebSession(Request $request, User $user): void
    {
        $plainToken = bin2hex(random_bytes(32));

        $user->apiTokens()->create([
            'name' => 'web',
            'token_hash' => hash('sha256', $plainToken),
        ]);

        $request->session()->regenerate();
        $request->session()->put('web_user_id', $user->id);
        $request->session()->put('web_api_token', $plainToken);
    }

    private function webUser(Request $request): ?User
    {
        $userId = $request->session()->get('web_user_id');

        if (! $userId) {
            return null;
        }

        return User::query()->find($userId);
    }
}
