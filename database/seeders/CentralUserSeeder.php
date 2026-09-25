<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\CentralUser;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CentralUserSeeder extends Seeder
{
    public function run(): void
    {
        CentralUser::firstOrCreate(
            ['email' => 'admin@multistore.test'],
            [
                'uuid' => (string) Str::uuid(),
                'name' => 'Super Admin',
                'password' => Hash::make('password'),
            ]
        );
    }
}
