<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Tenant;

use App\Models\Tenant;
use Dedoc\Scramble\Attributes\BodyParameter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

#[BodyParameter('domain', description: 'Novo domínio ou subdomínio do tenant (opcional)', type: 'string', example: 'novo-dominio.localhost')]
#[BodyParameter('is_active', description: 'Novo status de ativação do tenant (opcional)', type: 'boolean', example: false)]
#[BodyParameter('data', description: 'Novos atributos e metadados para mesclar com os existentes (opcional)', type: 'object', example: ['company' => 'Empresa Atualizada S.A.'])]
final class UpdateTenantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Tenant|string|null $tenant */
        $tenant = $this->route('tenant');
        $tenantId = $tenant instanceof Tenant ? $tenant->id : $tenant;

        return [
            'domain' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('domains', 'domain')->ignore($tenantId, 'tenant_id'),
            ],
            'is_active' => ['nullable', 'boolean'],
            'data' => ['nullable', 'array'],
        ];
    }
}
