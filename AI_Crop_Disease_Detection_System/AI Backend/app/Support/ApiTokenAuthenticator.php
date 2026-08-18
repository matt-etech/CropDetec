<?php

namespace App\Support;

use App\Models\ApiToken;
use App\Models\User;
use Illuminate\Http\Request;

class ApiTokenAuthenticator
{
    public function userFromRequest(Request $request): ?User
    {
        $plainToken = $request->bearerToken();

        if (! $plainToken) {
            return null;
        }

        $token = ApiToken::query()
            ->where('token_hash', hash('sha256', $plainToken))
            ->where(function ($query): void {
                $query->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            })
            ->first();

        if (! $token) {
            return null;
        }

        $token->forceFill(['last_used_at' => now()])->save();

        return $token->user;
    }
}
