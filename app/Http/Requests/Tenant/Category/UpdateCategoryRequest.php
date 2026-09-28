<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant\Category;

use App\Models\Category;
use Dedoc\Scramble\Attributes\BodyParameter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

#[BodyParameter('name', description: 'Novo nome da categoria (opcional)', type: 'string', example: 'Smartphones & Celulares')]
#[BodyParameter('slug', description: 'Novo slug amigável (opcional)', type: 'string', example: 'smartphones-celulares')]
#[BodyParameter('description', description: 'Nova descrição da categoria (opcional)', type: 'string')]
#[BodyParameter('parent_id', description: 'Novo ID da categoria pai (opcional)', type: 'integer')]
#[BodyParameter('position', description: 'Nova posição na ordenação (opcional)', type: 'integer')]
#[BodyParameter('is_active', description: 'Novo status de ativação (opcional)', type: 'boolean')]
#[BodyParameter('image_path', description: 'Novo caminho da imagem (opcional)', type: 'string')]
#[BodyParameter('meta', description: 'Novos metadados SEO (opcional)', type: 'object')]
final class UpdateCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        /** @var Category|string|null $category */
        $category = $this->route('category');

        return $this->user()?->can('update', $category) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Category|string|null $category */
        $category = $this->route('category');
        $categoryId = $category instanceof Category ? $category->id : $category;

        return [
            'name' => ['nullable', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('categories', 'slug')->ignore($categoryId),
            ],
            'description' => ['nullable', 'string'],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:categories,id',
                Rule::notIn([$categoryId]),
            ],
            'position' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'meta' => ['nullable', 'array'],
        ];
    }
}
