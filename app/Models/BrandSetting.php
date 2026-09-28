<?php

declare(strict_types=1);

namespace App\Models;

class BrandSetting extends BaseModel
{
    protected $fillable = [
        'uuid',
        'brand_name',
        'primary_color',
        'secondary_color',
        'accent_color',
        'background_color',
        'logo_url',
        'favicon_url',
        'banner_url',
        'font_family',
        'social_links',
        'custom_css',
    ];

    protected $casts = [
        'social_links' => 'array',
    ];
}
