<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant\Category;

use App\Models\Category;
use Dedoc\Scramble\Attributes\BodyParameter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

#[BodyParameter('name', description: 'Nome da categoria', type: 'string', example: 'Smartphones')]
#[BodyParameter('slug', description: 'Slug amigável para URL (gerado automaticamente se omitido)', type: 'string', example: 'smartphones')]
#[BodyParameter('description', description: 'Descrição da categoria', type: 'string', example: 'Celulares e smartphones de última geração')]
#[BodyParameter('parent_id', description: 'ID da categoria pai para subcategorias', type: 'integer', example: 1)]
#[BodyParameter('position', description: 'Ordem de exibição na loja', type: 'integer', example: 0)]
#[BodyParameter('is_active', description: 'Status de ativação da categoria', type: 'boolean', example: true)]
#[BodyParameter('image_path', description: 'Caminho ou URL da imagem/ícone da categoria', type: 'string', example: 'categories/smartphones.png')]
#[BodyParameter('meta', description: 'Objeto de metadados SEO ou atributos adicionais', type: 'object', example: ['meta_title' => 'Comprar Smartphones'])]
final class StoreCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('create', Category::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'slug' => ['nullable', 'string', 'max:255', 'unique:categories,slug'],
            'description' => ['nullable', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:categories,id'],
            'position' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
