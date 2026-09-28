<?php

declare(strict_types=1);

namespace App\Http\Requests\Tenant\Brand;

use App\Models\BrandSetting;
use Dedoc\Scramble\Attributes\BodyParameter;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

#[BodyParameter('brand_name', description: 'Nome exibido da marca do tenant', type: 'string', example: 'Loja Alpha')]
#[BodyParameter('primary_color', description: 'Cor primária (HEX ou HSL)', type: 'string', example: '#6366f1')]
#[BodyParameter('secondary_color', description: 'Cor secundária', type: 'string', example: '#8b5cf6')]
#[BodyParameter('accent_color', description: 'Cor de destaque/botões', type: 'string', example: '#f59e0b')]
#[BodyParameter('background_color', description: 'Cor de fundo principal', type: 'string', example: '#0f172a')]
#[BodyParameter('logo_url', description: 'URL ou caminho da imagem do logo', type: 'string', example: 'https://cdn.example.com/logo.png')]
#[BodyParameter('favicon_url', description: 'URL do favicon', type: 'string', example: 'https://cdn.example.com/favicon.ico')]
#[BodyParameter('banner_url', description: 'URL do banner principal', type: 'string', example: 'https://cdn.example.com/banner.png')]
#[BodyParameter('font_family', description: 'Família tipográfica principal', type: 'string', example: 'Inter')]
#[BodyParameter('social_links', description: 'Objeto de links de redes sociais', type: 'object', example: ['instagram' => '@lojaalpha'])]
#[BodyParameter('custom_css', description: 'CSS customizado adicional', type: 'string')]
final class UpdateBrandSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('update', BrandSetting::class) ?? false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'brand_name' => ['nullable', 'string', 'max:255'],
            'primary_color' => ['nullable', 'string', 'max:30'],
            'secondary_color' => ['nullable', 'string', 'max:30'],
            'accent_color' => ['nullable', 'string', 'max:30'],
            'background_color' => ['nullable', 'string', 'max:30'],
            'logo_url' => ['nullable', 'string', 'max:500'],
            'favicon_url' => ['nullable', 'string', 'max:500'],
            'banner_url' => ['nullable', 'string', 'max:500'],
            'font_family' => ['nullable', 'string', 'max:100'],
            'social_links' => ['nullable', 'array'],
            'custom_css' => ['nullable', 'string'],
        ];
    }
}
