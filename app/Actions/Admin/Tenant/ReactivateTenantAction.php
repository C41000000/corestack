<?php

declare(strict_types=1);

namespace App\Actions\Admin\Tenant;

use App\Models\Tenant;

final readonly class ReactivateTenantAction
{
    public function execute(Tenant $tenant): Tenant
    {
        $tenant->update(['is_active' => true]);

        return $tenant->refresh()->load('domains');
    }
}
