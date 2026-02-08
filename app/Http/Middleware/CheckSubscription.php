<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        // 1. Bypass Super Admins
        if ($user && $user->hasRole('super_admin')) {
            return $next($request);
        }

        // 2. Check vendor subscription
        if ($user && $user->vendor_id) {
            $vendor = $user->vendor;

            if ($vendor->status === 'suspended') {
                return response()->view('errors.suspended', [], 403);
            }

            if ($vendor->subscription_ends_at && $vendor->subscription_ends_at->isPast()) {
                return response()->view('errors.subscription_expired', [], 403);
            }
        }

        return $next($request);
    }
}
