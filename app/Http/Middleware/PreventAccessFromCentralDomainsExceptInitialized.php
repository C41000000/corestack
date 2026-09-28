<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Middleware\PreventAccessFromCentralDomains;

class PreventAccessFromCentralDomainsExceptInitialized
{
    public function handle(Request $request, Closure $next)
    {
        if (tenant()) {
            return $next($request);
        }

        return app(PreventAccessFromCentralDomains::class)->handle($request, $next);
    }
}
