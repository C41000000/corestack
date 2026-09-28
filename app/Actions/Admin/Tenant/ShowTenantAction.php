<?php

declare(strict_types=1);

namespace App\Actions\Admin\Tenant;

use App\Models\Tenant;

final readonly class ShowTenantAction
{
    public function execute(Tenant $tenant): Tenant
    {
        return $tenant->load('domains');
    }
}
