<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Tenant\Model\Tenant;

class TenantSeeder extends Seeder
{
    public function run(): void
    {
        $tenant1 = Tenant::firstOrCreate(
            ['id' => 'loja-alpha'],
            ['name' => 'Loja Alpha Exemplo']
        );

        $tenant1->domains()->firstOrCreate(
            ['domain' => 'alpha.localhost']
        );

        $tenant2 = Tenant::firstOrCreate(
            ['id' => 'loja-beta'],
            ['name' => 'Loja Beta Exemplo']
        );

        $tenant2->domains()->firstOrCreate(
            ['domain' => 'beta.localhost']
        );
    }
}
