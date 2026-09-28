<?php

declare(strict_types=1);

namespace App\DTOs\Tenant\Brand;

use App\Http\Requests\Tenant\Brand\UpdateBrandSettingRequest;

final readonly class UpdateBrandSettingDTO
{
    public function __construct(
        public ?string $brandName = null,
        public ?string $primaryColor = null,
        public ?string $secondaryColor = null,
        public ?string $accentColor = null,
        public ?string $backgroundColor = null,
        public ?string $logoUrl = null,
        public ?string $faviconUrl = null,
        public ?string $bannerUrl = null,
        public ?string $fontFamily = null,
        public ?array $socialLinks = null,
        public ?string $customCss = null,
    ) {}

    /**
     * @param  UpdateBrandSettingRequest|array<string, mixed>  $data
     */
    public static function fromRequest(UpdateBrandSettingRequest|array $data): self
    {
        $validated = $data instanceof UpdateBrandSettingRequest ? $data->validated() : $data;

        return new self(
            brandName: isset($validated['brand_name']) ? (string) $validated['brand_name'] : null,
            primaryColor: isset($validated['primary_color']) ? (string) $validated['primary_color'] : null,
            secondaryColor: isset($validated['secondary_color']) ? (string) $validated['secondary_color'] : null,
            accentColor: isset($validated['accent_color']) ? (string) $validated['accent_color'] : null,
            backgroundColor: isset($validated['background_color']) ? (string) $validated['background_color'] : null,
            logoUrl: isset($validated['logo_url']) ? (string) $validated['logo_url'] : null,
            faviconUrl: isset($validated['favicon_url']) ? (string) $validated['favicon_url'] : null,
            bannerUrl: isset($validated['banner_url']) ? (string) $validated['banner_url'] : null,
            fontFamily: isset($validated['font_family']) ? (string) $validated['font_family'] : null,
            socialLinks: isset($validated['social_links']) && is_array($validated['social_links']) ? $validated['social_links'] : null,
            customCss: isset($validated['custom_css']) ? (string) $validated['custom_css'] : null,
        );
    }

    /**
     * Convert DTO attributes to an array suitable for Eloquent update.
     *
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return array_filter([
            'brand_name' => $this->brandName,
            'primary_color' => $this->primaryColor,
            'secondary_color' => $this->secondaryColor,
            'accent_color' => $this->accentColor,
            'background_color' => $this->backgroundColor,
            'logo_url' => $this->logoUrl,
            'favicon_url' => $this->faviconUrl,
            'banner_url' => $this->bannerUrl,
            'font_family' => $this->fontFamily,
            'social_links' => $this->socialLinks,
            'custom_css' => $this->customCss,
        ], fn ($value) => $value !== null);
    }
}
