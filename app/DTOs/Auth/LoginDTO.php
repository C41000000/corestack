<?php

declare(strict_types=1);

namespace App\DTOs\Auth;

final readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public string $clientIpAddress,
    ) {}

    public static function fromRequest($request): self
    {
        return new self(
            email: $request->validated()['email'],
            password: $request->validated()['password'],
            clientIpAddress: $request->ip(),
        );
    }
}
