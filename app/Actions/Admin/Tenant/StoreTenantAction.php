<?php

namespace App\Actions\Admin\Tenant;

use App\DTOs\Admin\Tenant\StoreTenantDTO;
use App\Models\Tenant;

final readonly class StoreTenantAction {

    public function __construct(Tenant $model){}

    public function execute(StoreTenantDTO $dto): Tenant
    {

    }

}
