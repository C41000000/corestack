<?php

declare(strict_types=1);

namespace App\Http\Requests\Admin\Tenant;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

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
