<?php

declare(strict_types=1);

use App\Models\CentralUser;

it('generates uuid automatically on creation and returns route key and custom claims', function () {
    $user = CentralUser::create([
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'secret',
    ]);

    expect($user->uuid)->not->toBeEmpty();
    expect($user->getRouteKeyName())->toBe('uuid');
    expect($user->getJWTCustomClaims())->toBeArray();
});
