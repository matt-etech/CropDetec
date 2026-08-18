<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\LoginRequest;
use App\Http\Requests\Api\RegisterRequest;
use App\Http\Requests\Api\UpdateProfileRequest;
use App\Models\ApiToken;
use App\Models\User;
use App\Support\ApiTokenAuthenticator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => $data['password'],
            'language_preference' => $data['language_preference'] ?? 'en',
            'role' => 'farmer',
        ]);

        return response()->json([
            'message' => 'Account created successfully.',
            'user' => $user,
            'token' => $this->issueToken($user),
        ], 201);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::query()->where('email', $data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        return response()->json([
            'message' => 'Logged in successfully.',
            'user' => $user,
            'token' => $this->issueToken($user),
        ]);
    }

    public function me(Request $request, ApiTokenAuthenticator $auth): JsonResponse
    {
        $user = $auth->userFromRequest($request);

        abort_if(! $user, 401, 'Unauthenticated.');

        return response()->json(['user' => $user]);
    }

    public function update(UpdateProfileRequest $request, ApiTokenAuthenticator $auth): JsonResponse
    {
        $user = $auth->userFromRequest($request);

        abort_if(! $user, 401, 'Unauthenticated.');

        $data = $request->validated();

        $user->update($data);

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user->fresh(),
        ]);
    }

    public function logout(Request $request, ApiTokenAuthenticator $auth): JsonResponse
    {
        $user = $auth->userFromRequest($request);

        abort_if(! $user, 401, 'Unauthenticated.');

        $plainToken = $request->bearerToken();

        ApiToken::query()
            ->where('user_id', $user->id)
            ->where('token_hash', hash('sha256', $plainToken))
            ->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    private function issueToken(User $user): string
    {
        $plainToken = bin2hex(random_bytes(32));

        $user->apiTokens()->create([
            'name' => 'mobile',
            'token_hash' => hash('sha256', $plainToken),
        ]);

        return $plainToken;
    }
}
