<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant\Auth;

use Dedoc\Scramble\Attributes\BodyParameter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

#[BodyParameter('email', description: 'E-mail do usuário do tenant', type: 'string', example: 'admin@tenant.com')]
#[BodyParameter('password', description: 'Senha de acesso', type: 'string', example: 'password123')]
final class TenantLoginRequest extends FormRequest
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
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ];
    }
}
