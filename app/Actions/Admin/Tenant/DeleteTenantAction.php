<?php

declare(strict_types=1);

namespace App\Actions\Admin\Tenant;

use App\Models\Tenant;

final readonly class DeleteTenantAction
{
    public function execute(Tenant $tenant): bool
    {
        return $tenant->update(['is_active' => false]);
    }
}
