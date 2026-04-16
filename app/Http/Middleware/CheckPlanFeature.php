<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Config;

class CheckPlanFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $feature): Response
    {
        $activePlan = Config::get('system_plan.active_plan', 'enterprise');
        $features = Config::get("system_plan.plans.{$activePlan}", []);

        if (!isset($features[$feature]) || $features[$feature] !== true) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'هذه الميزة غير متاحة في باقتك الحالية.'], 403);
            }
            abort(403, 'عذراً، هذه الميزة غير متاحة في باقتك الحالية. لترقية باقتك يرجى التواصل مع الدعم الفني.');
        }

        return $next($request);
    }
}
