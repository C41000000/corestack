<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Tenant;

use Dedoc\Scramble\Attributes\BodyParameter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

#[BodyParameter('id', description: 'Identificador único do tenant (opcional, gera UUID se omitido)', type: 'string', example: 'empresa-acme')]
#[BodyParameter('domain', description: 'Domínio ou subdomínio do tenant (único)', type: 'string', example: 'acme.localhost')]
#[BodyParameter('is_active', description: 'Status de ativação do tenant', type: 'boolean', example: true)]
#[BodyParameter('data', description: 'Objeto com atributos e metadados adicionais', type: 'object', example: ['company' => 'Acme Corp'])]
final class StoreTenantRequest extends FormRequest
{
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
        return [
            'id' => ['nullable', 'string', 'max:255', 'unique:tenants,id'],
            'domain' => ['required', 'string', 'max:255', 'unique:domains,domain'],
            'is_active' => ['nullable', 'boolean'],
            'data' => ['nullable', 'array'],
        ];
    }
}
