<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Auth;

use App\DTOs\Tenant\Auth\TenantLoginDTO;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\JWTGuard;

final class TenantLoginAction
{
    /**
     * Attempt to authenticate a tenant user and return JWT access token.
     *
     * @return array{access_token: string, token_type: string, expires_in: int, user: User}
     */
    public function execute(TenantLoginDTO $dto): array
    {
        $guard = Auth::guard('tenant_api');

        if (! $token = $guard->attempt($dto->toArray())) {
            throw ValidationException::withMessages([
                'email' => [__('auth.failed')],
            ]);
        }

        /** @var User $user */
        $user = $guard->user();

        /** @var JWTGuard $guard */
        $factory = $guard->factory();

        return [
            'access_token' => (string) $token,
            'token_type' => 'bearer',
            'expires_in' => $factory->getTTL() * 60,
            'user' => $user,
        ];
    }
}
