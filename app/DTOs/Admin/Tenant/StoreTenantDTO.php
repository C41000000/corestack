<?php

declare(strict_types=1);

namespace App\DTOs\Admin\Tenant;

use App\Http\Requests\Admin\Tenant\StoreTenantRequest;

final readonly class StoreTenantDTO
{
    public function __construct(
        public string $domain,
        public ?string $id = null,
        public bool $isActive = true,
        public ?array $data = null,
    ) {}

    /**
     * @param  StoreTenantRequest|array<string, mixed>  $data
     */
    public static function fromRequest(StoreTenantRequest|array $data): self
    {
        $validated = $data instanceof StoreTenantRequest ? $data->validated() : $data;

        return new self(
            domain: (string) $validated['domain'],
            id: isset($validated['id']) ? (string) $validated['id'] : null,
            isActive: isset($validated['is_active']) ? (bool) $validated['is_active'] : true,
            data: isset($validated['data']) && is_array($validated['data']) ? $validated['data'] : null,
        );
    }
}
