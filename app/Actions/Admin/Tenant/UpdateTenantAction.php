<?php

declare(strict_types=1);

namespace App\Actions\Admin\Tenant;

use App\DTOs\Admin\Tenant\UpdateTenantDTO;
use App\Models\Tenant;

final readonly class UpdateTenantAction
{
    public function execute(Tenant $tenant, UpdateTenantDTO $dto): Tenant
    {
        $attributes = [];

        if ($dto->isActive !== null) {
            $attributes['is_active'] = $dto->isActive;
        }

        if ($dto->data !== null) {
            $attributes = array_merge($attributes, $dto->data);
        }

        if (! empty($attributes)) {
            $tenant->update($attributes);
        }

        if ($dto->domain !== null) {
            $primaryDomain = $tenant->domains()->first();
            if ($primaryDomain) {
                $primaryDomain->update(['domain' => $dto->domain]);
            } else {
                $tenant->createDomain(['domain' => $dto->domain]);
            }
        }

        return $tenant->refresh()->load('domains');
    }
}
