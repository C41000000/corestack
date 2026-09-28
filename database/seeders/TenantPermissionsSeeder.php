<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class TenantPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(TenantDatabaseSeeder::class);
    }
}
