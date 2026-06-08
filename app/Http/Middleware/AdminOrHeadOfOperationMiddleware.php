<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminOrHeadOfOperationMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!$request->user()) {
            abort(403);
        }

        $role = $request->user()->role;
        if ($role !== UserRole::Admin && $role !== UserRole::HeadOfOperation) {
            abort(403, 'Admin or Head of Operation access required.');
        }

        return $next($request);
    }
}
