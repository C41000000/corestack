<?php

declare(strict_types=1);

use App\Models\CentralUser;
use Illuminate\Support\Facades\Hash;

it('should allow central user to login with valid credentials', function () {
    $password = 'Secret123!';
    CentralUser::factory()->create([
        'email' => 'central@example.com',
        'password' => Hash::make($password),
    ]);

    $response = $this->postJson('/api/v1/auth/central-login', [
        'email' => 'central@example.com',
        'password' => $password,
    ]);

    $response->assertStatus(200);
});

it('should reject login with invalid credentials', function () {
    CentralUser::factory()->create([
        'email' => 'central@example.com',
        'password' => Hash::make('password'),
    ]);

    $response = $this->postJson('/api/v1/auth/central-login', [
        'email' => 'central@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

it('should return 429 when rate limit is exceeded', function () {
    for ($i = 0; $i < 5; $i++) {
        $this->postJson('/api/v1/auth/central-login', [
            'email' => 'central@example.com',
            'password' => 'wrong-password',
        ]);
    }

    $response = $this->postJson('/api/v1/auth/central-login', [
        'email' => 'central@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertStatus(429);
});
