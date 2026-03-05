<?php

namespace Endone777\Roles\Middleware;

use Closure;
use Illuminate\Http\Request;
use Endone777\Roles\Exceptions\RoleDeniedException;
use Symfony\Component\HttpFoundation\Response;

class VerifyRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (auth()->check() && auth()->user()->hasRole($role)) {
            return $next($request);
        }

        throw new RoleDeniedException($role);
    }
}
