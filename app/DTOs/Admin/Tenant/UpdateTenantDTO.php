<?php

declare(strict_types=1);

namespace App\DTOs\Admin\Tenant;

use App\Http\Requests\Admin\Tenant\UpdateTenantRequest;

final readonly class UpdateTenantDTO
{
    public function __construct(
        public ?string $domain = null,
        public ?bool $isActive = null,
        public ?array $data = null,
    ) {}

    /**
     * @param  UpdateTenantRequest|array<string, mixed>  $data
     */
    public static function fromRequest(UpdateTenantRequest|array $data): self
    {
        $validated = $data instanceof UpdateTenantRequest ? $data->validated() : $data;

        return new self(
            domain: isset($validated['domain']) ? (string) $validated['domain'] : null,
            isActive: isset($validated['is_active']) ? (bool) $validated['is_active'] : null,
            data: isset($validated['data']) && is_array($validated['data']) ? $validated['data'] : null,
        );
    }
}
