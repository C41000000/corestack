<?php

declare(strict_types=1);

namespace App\DTOs\Tenant\Auth;

use App\Http\Requests\Tenant\Auth\TenantLoginRequest;

final readonly class TenantLoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
    ) {}

    public static function fromRequest(TenantLoginRequest $request): self
    {
        return new self(
            email: (string) $request->validated('email'),
            password: (string) $request->validated('password'),
        );
    }

    public function toArray(): array
    {
        return [
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
