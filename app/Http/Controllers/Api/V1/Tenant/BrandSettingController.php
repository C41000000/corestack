<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Tenant;

use App\Actions\Tenant\Brand\ShowBrandSettingAction;
use App\Actions\Tenant\Brand\UpdateBrandSettingAction;
use App\DTOs\Tenant\Brand\UpdateBrandSettingDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\Brand\UpdateBrandSettingRequest;
use App\Http\Resources\Tenant\BrandSettingResource;
use Dedoc\Scramble\Attributes\Endpoint;
use Dedoc\Scramble\Attributes\Group;

#[Group(name: 'Tenant / Identidade Visual', description: 'Gerenciamento da Marca e Personalização do Tenant')]
final class BrandSettingController extends Controller
{
    #[Endpoint(title: 'Exibir Identidade da Marca', description: 'Retorna as configurações de marca, cores, logos e CSS do tenant.')]
    public function show(ShowBrandSettingAction $action): BrandSettingResource
    {
        $setting = $action->execute();

        return new BrandSettingResource($setting);
    }

    #[Endpoint(title: 'Atualizar Identidade da Marca', description: 'Atualiza as configurações de marca, cores, logo, favicon e temas do tenant.')]
    public function update(UpdateBrandSettingRequest $request, UpdateBrandSettingAction $action): BrandSettingResource
    {
        $setting = $action->execute(UpdateBrandSettingDTO::fromRequest($request));

        return new BrandSettingResource($setting);
    }
}
