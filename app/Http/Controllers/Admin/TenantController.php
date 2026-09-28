<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Tenant\DeleteTenantAction;
use App\Actions\Admin\Tenant\ListTenantsAction;
use App\Actions\Admin\Tenant\ShowTenantAction;
use App\Actions\Admin\Tenant\StoreTenantAction;
use App\Actions\Admin\Tenant\UpdateTenantAction;
use App\DTOs\Admin\Tenant\StoreTenantDTO;
use App\DTOs\Admin\Tenant\UpdateTenantDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Tenant\StoreTenantRequest;
use App\Http\Requests\Admin\Tenant\UpdateTenantRequest;
use App\Http\Resources\Admin\TenantResource;
use App\Models\Tenant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class TenantController extends Controller
{
    public function index(Request $request, ListTenantsAction $action): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $search = $request->query('search');
        $searchString = is_string($search) ? $search : null;
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : null;

        $tenants = $action->execute($perPage, $searchString, $isActive);

        return TenantResource::collection($tenants);
    }

    public function store(StoreTenantRequest $request, StoreTenantAction $action): TenantResource
    {
        $tenant = $action->execute(StoreTenantDTO::fromRequest($request));

        return new TenantResource($tenant);
    }

    public function show(Tenant $tenant, ShowTenantAction $action): TenantResource
    {
        return new TenantResource($action->execute($tenant));
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant, UpdateTenantAction $action): TenantResource
    {
        $updatedTenant = $action->execute($tenant, UpdateTenantDTO::fromRequest($request));

        return new TenantResource($updatedTenant);
    }

    public function destroy(Tenant $tenant, DeleteTenantAction $action): JsonResponse
    {
        $action->execute($tenant);

        return response()->json([
            'message' => 'Tenant deleted successfully.',
        ]);
    }
}
