<?php

declare(strict_types=1);

use App\Actions\Auth\AuthenticateCentralUser;
use App\DTOs\Auth\LoginDTO;
use App\Models\CentralUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

it('should authenticate central user with valid credentials and clear rate limiter', function () {
    $password = 'Secret123!';
    CentralUser::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make($password),
    ]);

    $dto = new LoginDTO(email: 'USER@example.com ', password: $password, clientIpAddress: '127.0.0.1');
    $action = new AuthenticateCentralUser;

    $token = $action->execute($dto);

    expect($token)->toBeString()->not->toBeEmpty();
});

it('should fail authentication with invalid credentials and hit rate limiter', function () {
    CentralUser::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('correct-password'),
    ]);

    $dto = new LoginDTO(email: 'user@example.com', password: 'wrong-password', clientIpAddress: '127.0.0.1');
    $action = new AuthenticateCentralUser;

    expect(fn () => $action->execute($dto))
        ->toThrow(ValidationException::class);
});

it('should lock out user after maximum failed attempts', function () {
    $dto = new LoginDTO(
        email: 'user@example.com',
        password: 'wrong-password',
        clientIpAddress: '127.0.0.1'
    );

    $action = new AuthenticateCentralUser;

    for ($i = 0; $i < 5; $i++) {
        try {
            $action->execute($dto);
        } catch (ValidationException) {
        }
    }

    try {
        $action->execute($dto);
        $this->fail('Expected ValidationException with status 429');
    } catch (ValidationException $e) {
        expect($e->status)->toBe(429);
    }
});

it('should throttle based on specific client ip address', function () {
    $action = new AuthenticateCentralUser;

    $dtoIpA = new LoginDTO(
        email: 'user@example.com',
        password: 'wrong-password',
        clientIpAddress: '192.168.1.10'
    );

    $dtoIpB = new LoginDTO(
        email: 'user@example.com',
        password: 'wrong-password',
        clientIpAddress: '10.0.0.5'
    );

    for ($i = 0; $i < 5; $i++) {
        try {
            $action->execute($dtoIpA);
        } catch (ValidationException) {
        }
    }

    try {
        $action->execute($dtoIpA);
        $this->fail('Expected rate limit for IP A');
    } catch (ValidationException $e) {
        expect($e->status)->toBe(429);
    }

    try {
        $action->execute($dtoIpB);

    } catch (ValidationException $e) {
        expect($e->status)->not->toBe(429);
    }
});
