<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckOnboarding
{
    public function handle(Request $request, Closure $next): mixed
    {
        $user = $request->user();

        if (
            $user &&
            ! $user->isSuperAdmin() &&
            ! $user->onboarding_completed_at &&
            ! $request->routeIs('onboarding.*') &&
            ! $request->routeIs('logout')
        ) {
            return redirect()->route('onboarding.index');
        }

        return $next($request);
    }
}
