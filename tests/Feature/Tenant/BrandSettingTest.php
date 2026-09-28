<?php

declare(strict_types=1);

use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\TenantPermissionsSeeder;

beforeEach(function () {
    $this->tenant = Tenant::create(['id' => 'brand-test', 'is_active' => true]);
    $this->tenant->createDomain(['domain' => 'brand.localhost']);

    tenancy()->initialize($this->tenant);

    $this->seed(TenantPermissionsSeeder::class);
    $this->adminUser = User::where('email', 'admin@tenant.com')->first();
});

it('fetches brand settings publicly', function () {
    $response = $this->getJson('http://brand.localhost/api/v1/brand-settings');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                'id',
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
                'updated_at',
            ],
        ]);
});

it('forbids updating brand settings for unauthorized users', function () {
    $regularUser = User::create([
        'name' => 'Regular User',
        'email' => 'regular@tenant.com',
        'password' => 'password123',
    ]);

    $this->actingAs($regularUser, 'tenant_api');

    $response = $this->putJson('http://brand.localhost/api/v1/brand-settings', [
        'brand_name' => 'Nova Marca Hack',
    ]);

    $response->assertStatus(403);
});

it('updates brand settings successfully for authorized admin', function () {
    $this->actingAs($this->adminUser, 'tenant_api');

    $payload = [
        'brand_name' => 'CoreStack Store',
        'primary_color' => '#6366f1',
        'secondary_color' => '#8b5cf6',
        'accent_color' => '#f59e0b',
        'background_color' => '#0f172a',
        'logo_url' => 'https://example.com/logo.png',
        'favicon_url' => 'https://example.com/favicon.ico',
        'banner_url' => 'https://example.com/banner.png',
        'font_family' => 'Inter',
        'social_links' => ['instagram' => '@corestack'],
        'custom_css' => 'body { color: #fff; }',
    ];

    $response = $this->putJson('http://brand.localhost/api/v1/brand-settings', $payload);

    $response->assertStatus(200)
        ->assertJsonPath('data.brand_name', 'CoreStack Store')
        ->assertJsonPath('data.primary_color', '#6366f1')
        ->assertJsonPath('data.logo_url', 'https://example.com/logo.png')
        ->assertJsonPath('data.social_links.instagram', '@corestack')
        ->assertJsonPath('data.custom_css', 'body { color: #fff; }');

    $this->assertDatabaseHas('brand_settings', [
        'brand_name' => 'CoreStack Store',
        'primary_color' => '#6366f1',
    ]);
});
