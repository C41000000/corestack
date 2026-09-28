<?php

declare(strict_types=1);

use App\Models\CentralUser;
use App\Models\Tenant;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    foreach (glob(database_path('tenant*')) as $file) {
        if (is_file($file)) {
            @unlink($file);
        }
    }

    $this->user = CentralUser::factory()->create([
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
    ]);
    $this->token = auth('api')->login($this->user);
    $this->headers = [
        'Authorization' => "Bearer {$this->token}",
    ];
});

afterEach(function () {
    foreach (glob(database_path('tenant*')) as $file) {
        if (is_file($file)) {
            @unlink($file);
        }
    }
});

it('denies unauthenticated access to tenant endpoints', function () {
    auth('api')->logout();
    $response = $this->getJson('/api/admin/tenants');
    $response->assertStatus(401);
});

it('lists tenants for authenticated user', function () {
    $tenant = Tenant::create(['id' => 'foo', 'is_active' => true]);
    $tenant->createDomain(['domain' => 'foo.localhost']);

    $response = $this->getJson('/api/admin/tenants', $this->headers);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'is_active', 'data', 'domains', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ]);
});

it('creates a tenant successfully', function () {
    $payload = [
        'id' => 'acme-corp',
        'domain' => 'acme.localhost',
        'is_active' => true,
        'data' => ['company' => 'Acme Corp'],
    ];

    $response = $this->postJson('/api/admin/tenants', $payload, $this->headers);
    $response->assertStatus(201)
        ->assertJsonPath('id', 'acme-corp')
        ->assertJsonPath('is_active', true)
        ->assertJsonPath('data.company', 'Acme Corp')
        ->assertJsonPath('domains.0', 'acme.localhost');

    $this->assertDatabaseHas('tenants', [
        'id' => 'acme-corp',
        'is_active' => true,
    ]);

    $this->assertDatabaseHas('domains', [
        'domain' => 'acme.localhost',
        'tenant_id' => 'acme-corp',
    ]);
});

it('fails to create a tenant with duplicate domain', function () {
    $tenant = Tenant::create(['id' => 'existing']);
    $tenant->createDomain(['domain' => 'shared.localhost']);

    $payload = [
        'domain' => 'shared.localhost',
    ];

    $response = $this->postJson('/api/admin/tenants', $payload, $this->headers);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['domain']);
});

it('shows a specific tenant', function () {
    $tenant = Tenant::create(['id' => 'show-me', 'is_active' => true]);
    $tenant->createDomain(['domain' => 'showme.localhost']);

    $response = $this->getJson("/api/admin/tenants/{$tenant->id}", $this->headers);

    $response->assertStatus(200)
        ->assertJsonPath('id', 'show-me')
        ->assertJsonPath('domains.0', 'showme.localhost');
});

it('updates a tenant successfully', function () {
    $tenant = Tenant::create(['id' => 'update-me', 'is_active' => true, 'name' => 'Original']);
    $tenant->createDomain(['domain' => 'old.localhost']);

    $payload = [
        'domain' => 'new.localhost',
        'is_active' => false,
        'data' => ['name' => 'Updated'],
    ];

    $response = $this->putJson("/api/admin/tenants/{$tenant->id}", $payload, $this->headers);

    $response->assertStatus(200)
        ->assertJsonPath('id', 'update-me')
        ->assertJsonPath('is_active', false)
        ->assertJsonPath('data.name', 'Updated')
        ->assertJsonPath('domains.0', 'new.localhost');

    $this->assertDatabaseHas('domains', [
        'domain' => 'new.localhost',
        'tenant_id' => 'update-me',
    ]);
});

it('deactivates a tenant on delete endpoint', function () {
    $tenant = Tenant::create(['id' => 'delete-me', 'is_active' => true]);
    $tenant->createDomain(['domain' => 'todelete.localhost']);

    $response = $this->deleteJson("/api/admin/tenants/{$tenant->id}", [], $this->headers);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Tenant deleted successfully.']);

    $this->assertDatabaseHas('tenants', [
        'id' => 'delete-me',
        'is_active' => false,
    ]);
});
