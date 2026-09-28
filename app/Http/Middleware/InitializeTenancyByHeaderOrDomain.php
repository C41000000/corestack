<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Tenant;
use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\InitializeTenancyByDomain;

class InitializeTenancyByHeaderOrDomain
{
    public function __construct(
        protected InitializeTenancyByDomain $domainInitializer
    ) {}

    public function handle(Request $request, Closure $next)
    {
        $tenantDomain = $request->header('X-Tenant-Domain');

        if ($tenantDomain && ! tenant()) {
            $tenant = Tenant::whereHas('domains', function ($query) use ($tenantDomain) {
                $query->where('domain', $tenantDomain);
            })->first();

            if ($tenant) {
                tenancy()->initialize($tenant);

                return $next($request);
            }
        }

        return $this->domainInitializer->handle($request, $next);
    }
}
