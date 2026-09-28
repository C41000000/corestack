<?php

declare(strict_types=1);

use App\Http\Middleware\EnsureTenantIsActive;
use App\Models\Tenant;
use Illuminate\Http\Request;

it('allows request when tenant is active', function () {
    $tenant = new Tenant(['id' => 'active-tenant', 'is_active' => true]);
    tenancy()->initialize($tenant);

    $middleware = new EnsureTenantIsActive;
    $request = Request::create('/', 'GET');

    $response = $middleware->handle($request, fn () => response('OK'));

    expect($response->getContent())->toBe('OK')
        ->and($response->getStatusCode())->toBe(200);

    tenancy()->end();
});

it('blocks request with 403 when tenant is inactive', function () {
    $tenant = new Tenant(['id' => 'inactive-tenant', 'is_active' => false]);
    tenancy()->initialize($tenant);

    $middleware = new EnsureTenantIsActive;
    $request = Request::create('/', 'GET');

    $response = $middleware->handle($request, fn () => response('OK'));

    expect($response->getStatusCode())->toBe(403)
        ->and(json_decode($response->getContent(), true))->toBe(['message' => 'Tenant is inactive.']);

    tenancy()->end();
});
