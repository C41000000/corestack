<?php

declare(strict_types=1);

it('returns validation error when login payload is empty', function () {
    $response = $this->postJson('/api/v1/auth/central-login');

    $response->assertStatus(422);
});
