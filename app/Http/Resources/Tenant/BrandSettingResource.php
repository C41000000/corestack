<?php

declare(strict_types=1);

namespace App\Http\Resources\Tenant;

use App\Models\BrandSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin BrandSetting */
final class BrandSettingResource extends JsonResource
{
    /**
     * The "data" wrapper that should be applied.
     *
     * @var string|null
     */
    public static $wrap = 'data';

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'brand_name' => $this->brand_name,
            'primary_color' => $this->primary_color,
            'secondary_color' => $this->secondary_color,
            'accent_color' => $this->accent_color,
            'background_color' => $this->background_color,
            'logo_url' => $this->logo_url,
            'favicon_url' => $this->favicon_url,
            'banner_url' => $this->banner_url,
            'font_family' => $this->font_family,
            'social_links' => $this->social_links,
            'custom_css' => $this->custom_css,
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
