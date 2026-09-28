<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\BrandSetting;
use App\Models\User;

class BrandSettingPolicy
{
    public function viewAny(?User $user): bool
    {
        return true;
    }

    public function view(?User $user, ?BrandSetting $brandSetting = null): bool
    {
        return true;
    }

    public function update(?User $user, ?BrandSetting $brandSetting = null): bool
    {
        return $user?->hasPermissionTo('brand_settings.update') ?? false;
    }
}
