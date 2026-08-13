<?php

namespace App\Http\Middleware;

use Closure;

class EnsureUserIsAdmin
{
    public function handle($request, Closure $next)
    {
        $user = $request->user();

        if (!$user || !$user->isAdmin()) {
            return response()->json([
                'status'  => false,
                'message' => 'Acceso denegado. Este endpoint es solo para administradores.',
            ], 403);
        }

        return $next($request);
    }
}
