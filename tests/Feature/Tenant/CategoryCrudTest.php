<?php

declare(strict_types=1);

use App\Models\Category;
use App\Models\Tenant;
use App\Models\User;
use Database\Seeders\TenantPermissionsSeeder;

beforeEach(function () {

    $this->tenant = Tenant::create(['id' => 'store-test', 'is_active' => true]);
    $this->tenant->createDomain(['domain' => 'store.localhost']);

    tenancy()->initialize($this->tenant);

    $this->seed(TenantPermissionsSeeder::class);
    $this->adminUser = User::where('email', 'admin@tenant.com')->first();
    $this->actingAs($this->adminUser);
});

it('forbids category access for unauthorized users', function () {
    $regularUser = User::create([
        'name' => 'Regular User',
        'email' => 'regular@tenant.com',
        'password' => 'password123',
    ]);

    $this->actingAs($regularUser);

    $this->getJson('http://store.localhost/api/v1/categories')->assertStatus(403);
    $this->postJson('http://store.localhost/api/v1/categories', ['name' => 'Test'])->assertStatus(403);
});

it('lists categories inside tenant context for authorized admin', function () {
    $parent = Category::create(['name' => 'Eletrônicos', 'position' => 1]);
    $child = Category::create(['name' => 'Smartphones', 'parent_id' => $parent->id, 'position' => 2]);

    $response = $this->getJson('http://store.localhost/api/v1/categories');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'uuid', 'name', 'slug', 'description', 'parent_id', 'position', 'is_active', 'image_path', 'meta', 'created_at', 'updated_at'],
            ],
            'links',
            'meta',
        ]);
});

it('creates a category with auto slug generation for authorized admin', function () {
    $payload = [
        'name' => 'Smartphones & Celulares',
        'description' => 'Dispositivos móveis',
        'position' => 10,
        'is_active' => true,
        'meta' => ['meta_title' => 'Os melhores smartphones'],
    ];

    $response = $this->postJson('http://store.localhost/api/v1/categories', $payload);

    $response->assertStatus(201)
        ->assertJsonPath('name', 'Smartphones & Celulares')
        ->assertJsonPath('slug', 'smartphones-celulares')
        ->assertJsonPath('position', 10)
        ->assertJsonPath('is_active', true)
        ->assertJsonPath('meta.meta_title', 'Os melhores smartphones');

    $this->assertDatabaseHas('categories', [
        'name' => 'Smartphones & Celulares',
        'slug' => 'smartphones-celulares',
    ]);
});

it('shows a category by uuid for authorized admin', function () {
    $category = Category::create(['name' => 'Computadores']);

    $response = $this->getJson("http://store.localhost/api/v1/categories/{$category->uuid}");

    $response->assertStatus(200)
        ->assertJsonPath('uuid', $category->uuid)
        ->assertJsonPath('name', 'Computadores');
});

it('updates a category successfully for authorized admin', function () {
    $category = Category::create(['name' => 'Informática']);

    $payload = [
        'name' => 'Informática & Gamer',
        'position' => 5,
    ];

    $response = $this->putJson("http://store.localhost/api/v1/categories/{$category->uuid}", $payload);

    $response->assertStatus(200)
        ->assertJsonPath('name', 'Informática & Gamer')
        ->assertJsonPath('slug', 'informatica-gamer')
        ->assertJsonPath('position', 5);

    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'name' => 'Informática & Gamer',
    ]);
});

it('soft deletes a category successfully for authorized admin', function () {
    $category = Category::create(['name' => 'Acessórios']);

    $response = $this->deleteJson("http://store.localhost/api/v1/categories/{$category->uuid}");

    $response->assertStatus(200)
        ->assertJson(['message' => 'Category deleted successfully.']);

    $this->assertSoftDeleted('categories', [
        'id' => $category->id,
    ]);
});
