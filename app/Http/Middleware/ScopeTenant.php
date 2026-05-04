<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ScopeTenant
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        // Super admins are not scoped to a single tenant
        if ($user && $user->role !== 'super_admin' && ! $user->tenant_id) {
            abort(403, 'No tenant assigned.');
        }

        return $next($request);
    }
}
