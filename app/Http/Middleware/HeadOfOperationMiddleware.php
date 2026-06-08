<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HeadOfOperationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user() || $request->user()->role !== UserRole::HeadOfOperation) {
            abort(403, 'Head of Operation access required.');
        }

        return $next($request);
    }
}
