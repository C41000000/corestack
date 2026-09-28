<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\Tenant\DeleteTenantAction;
use App\Actions\Admin\Tenant\ListTenantsAction;
use App\Actions\Admin\Tenant\ReactivateTenantAction;
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
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;
use Dedoc\Scramble\Attributes\QueryParameter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

#[Group(name: 'Admin / Tenants', description: 'Endpoints para gerenciamento de Tenants')]
final class TenantController extends Controller
{
    #[Endpoint(title: 'Listar Tenants', description: 'Retorna a lista paginada de todos os tenants cadastrados.')]
    #[QueryParameter(name: 'search', description: 'Busca por ID do tenant ou por domínio', type: 'string')]
    #[QueryParameter(name: 'is_active', description: 'Filtra pelo status ativo (true/false)', type: 'boolean')]
    #[QueryParameter(name: 'per_page', description: 'Quantidade de registros por página (padrão: 15)', type: 'integer')]
    public function index(Request $request, ListTenantsAction $action): AnonymousResourceCollection
    {
        $perPage = (int) $request->query('per_page', 15);
        $search = $request->query('search');
        $searchString = is_string($search) ? $search : null;
        $isActive = $request->has('is_active') ? $request->boolean('is_active') : null;

        $tenants = $action->execute($perPage, $searchString, $isActive);

        return TenantResource::collection($tenants);
    }

    #[Endpoint(title: 'Criar Tenant', description: 'Cria um novo tenant e seu domínio principal, inicializando o banco de dados do tenant.')]
    public function store(StoreTenantRequest $request, StoreTenantAction $action): TenantResource
    {
        $tenant = $action->execute(StoreTenantDTO::fromRequest($request));

        return new TenantResource($tenant);
    }

    #[Endpoint(title: 'Exibir Tenant', description: 'Retorna os detalhes de um tenant específico pelo seu ID.')]
    public function show(Tenant $tenant, ShowTenantAction $action): TenantResource
    {
        return new TenantResource($action->execute($tenant));
    }

    #[Endpoint(title: 'Atualizar Tenant', description: 'Atualiza o status, metadados ou domínio de um tenant existente.')]
    public function update(UpdateTenantRequest $request, Tenant $tenant, UpdateTenantAction $action): TenantResource
    {
        $updatedTenant = $action->execute($tenant, UpdateTenantDTO::fromRequest($request));

        return new TenantResource($updatedTenant);
    }

    #[Endpoint(title: 'Inativar Tenant', description: 'Inativa um tenant alterando o status is_active para false.')]
    public function destroy(Tenant $tenant, DeleteTenantAction $action): JsonResponse
    {
        $action->execute($tenant);

        return response()->json([
            'message' => 'Tenant deleted successfully.',
        ]);
    }

    #[Endpoint(title: 'Reativar Tenant', description: 'Reativa um tenant inativado alterando o status is_active para true.')]
    public function reactivate(Tenant $tenant, ReactivateTenantAction $action): TenantResource
    {
        $reactivatedTenant = $action->execute($tenant);

        return new TenantResource($reactivatedTenant);
    }
}
