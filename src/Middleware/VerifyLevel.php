<?php

namespace Endone777\Roles\Middleware;

use Closure;
use Illuminate\Http\Request;
use Endone777\Roles\Exceptions\LevelDeniedException;
use Symfony\Component\HttpFoundation\Response;

class VerifyLevel
{
    public function handle(Request $request, Closure $next, int $level): Response
    {
        if (auth()->check() && auth()->user()->level() >= $level) {
            return $next($request);
        }

        throw new LevelDeniedException($level);
    }
}
