<?php

declare(strict_types=1);

namespace App\Actions\Admin\Tenant;

use App\DTOs\Admin\Tenant\StoreTenantDTO;
use App\Models\Tenant;

final readonly class StoreTenantAction
{
    public function execute(StoreTenantDTO $dto): Tenant
    {
        $attributes = [
            'is_active' => $dto->isActive,
        ];

        if ($dto->id !== null) {
            $attributes['id'] = $dto->id;
        }

        if ($dto->data !== null) {
            $attributes = array_merge($attributes, $dto->data);
        }

        /** @var Tenant $tenant */
        $tenant = Tenant::create($attributes);

        $tenant->createDomain([
            'domain' => $dto->domain,
        ]);

        return $tenant->refresh()->load('domains');
    }
}
