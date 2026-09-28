<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

final class EnsureTenantIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        if (tenant() && ! tenant('is_active')) {
            return response()->json([
                'message' => 'Tenant is inactive.',
            ], 403);
        }

        return $next($request);
    }
}
