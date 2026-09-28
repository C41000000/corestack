<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Brand;

use App\Models\BrandSetting;

final readonly class ShowBrandSettingAction
{
    public function execute(): BrandSetting
    {
        /** @var BrandSetting|null $setting */
        $setting = BrandSetting::first();

        if (! $setting) {
            $setting = BrandSetting::create([]);
            $setting->refresh();
        }

        $setting->wasRecentlyCreated = false;

        return $setting;
    }
}
