<?php

namespace Endone777\Roles\Middleware;

use Closure;
use Illuminate\Http\Request;
use Endone777\Roles\Exceptions\PermissionDeniedException;
use Symfony\Component\HttpFoundation\Response;

class VerifyPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (auth()->check() && auth()->user()->hasPermission($permission)) {
            return $next($request);
        }

        throw new PermissionDeniedException($permission);
    }
}
