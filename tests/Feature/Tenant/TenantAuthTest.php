<?php

declare(strict_types=1);

use App\Models\Tenant;
use Database\Seeders\TenantPermissionsSeeder;

beforeEach(function () {

    $this->tenant = Tenant::create(['id' => 'store-auth-test', 'is_active' => true]);
    $this->tenant->createDomain(['domain' => 'auth.localhost']);

    tenancy()->initialize($this->tenant);
    $this->seed(TenantPermissionsSeeder::class);
});

it('authenticates a tenant user with valid credentials', function () {
    $response = $this->postJson('http://auth.localhost/api/v1/auth/login', [
        'email' => 'admin@tenant.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in',
            'user' => ['id', 'uuid', 'name', 'email', 'roles', 'permissions'],
        ]);
});

it('rejects login with invalid credentials', function () {
    $response = $this->postJson('http://auth.localhost/api/v1/auth/login', [
        'email' => 'admin@tenant.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('returns authenticated user profile on me endpoint', function () {
    $loginResponse = $this->postJson('http://auth.localhost/api/v1/auth/login', [
        'email' => 'admin@tenant.com',
        'password' => 'password123',
    ]);

    $token = $loginResponse->json('access_token');

    $response = $this->withHeader('Authorization', "Bearer {$token}")
        ->getJson('http://auth.localhost/api/v1/auth/me');

    $response->assertStatus(200)
        ->assertJsonPath('email', 'admin@tenant.com')
        ->assertJsonPath('roles.0', 'admin');
});
