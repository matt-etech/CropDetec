<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;

class AdminAuthorizer
{
    public function __construct(private readonly ApiTokenAuthenticator $authenticator)
    {
    }

    public function user(Request $request): User
    {
        $user = $this->authenticator->userFromRequest($request);

        abort_if(! $user, 401, 'Unauthenticated.');
        abort_if($user->role !== 'admin', 403, 'Administrator access is required.');

        return $user;
    }
}
