<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Brand;

use App\DTOs\Tenant\Brand\UpdateBrandSettingDTO;
use App\Models\BrandSetting;

final readonly class UpdateBrandSettingAction
{
    public function execute(UpdateBrandSettingDTO $dto): BrandSetting
    {
        /** @var BrandSetting|null $setting */
        $setting = BrandSetting::first();

        if (! $setting) {
            $setting = BrandSetting::create([]);
        }

        $setting->update($dto->toArray());

        return $setting->fresh();
    }
}
