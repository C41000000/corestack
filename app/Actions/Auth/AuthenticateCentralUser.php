<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\DTOs\Auth\LoginDTO;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class AuthenticateCentralUser
{
    /**
     * Maximum number of failed attempts allowed before throttling.
     */
    protected int $maxAttempts = 5;

    /**
     * Lockout decay time in seconds.
     */
    protected int $decaySeconds = 60;

    /**
     * Execute central user authentication with rate limiting protection.
     *
     * @throws ValidationException
     */
    public function execute(LoginDTO $dto): string
    {
        $throttleKey = $this->throttleKey($dto->email, $dto->clientIpAddress);

        $this->ensureIsNotRateLimited($throttleKey);

        $credentials = [
            'email' => Str::lower(trim($dto->email)),
            'password' => $dto->password,
        ];

        /** @var string|false $token */
        $token = Auth::guard('api')->attempt($credentials);

        if (! $token) {
            RateLimiter::hit($throttleKey, $this->decaySeconds);

            throw ValidationException::withMessages([
                'email' => [trans('auth.failed')],
            ]);
        }

        RateLimiter::clear($throttleKey);

        return $token;
    }

    /**
     * Ensure the login request is not rate limited.
     *
     * @throws ValidationException
     */
    protected function ensureIsNotRateLimited(string $throttleKey): void
    {
        if (! RateLimiter::tooManyAttempts($throttleKey, $this->maxAttempts)) {
            return;
        }

        // Se realmente quiser disparar o evento de Lockout sem depender do request global:
        // event(new Lockout($request)); -> Como estamos numa Action, podemos omitir ou passar o IP/email se houver listener escutando.

        $seconds = RateLimiter::availableIn($throttleKey);

        throw ValidationException::withMessages([
            'email' => [trans('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ])],
        ])->status(429);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    protected function throttleKey(string $email, string $clientIpAddress): string
    {
        return Str::transliterate(Str::lower(trim($email))).'|'.$clientIpAddress;
    }
}
