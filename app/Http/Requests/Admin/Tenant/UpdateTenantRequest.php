<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Tenant;

use App\Models\Tenant;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
