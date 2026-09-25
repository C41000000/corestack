<?php

namespace App\DTOs\Admin\Tenant;

use App\Http\Requests\Admin\Tenant\StoreTenantRequest;

final readonly class StoreTenantDTO {

    public static function fromRequest(StoreTenantRequest $request): self
    {
        return new self([

        ]);
    }

}
