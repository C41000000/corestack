<?php

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Tenant\StoreTenantAction;
use App\DTOs\Admin\Tenant\StoreTenantDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tenant\StoreTenantRequest;
use App\Http\Resources\Admin\TenantResource;

final class TenantController extends Controller
{
    public function store(StoreTenantRequest $request, StoreTenantAction $action)
    {
        return new TenantResource($action->execute(StoreTenantDTO::fromRequest($request->validated())));
    }
}
