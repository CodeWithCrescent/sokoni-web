<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WebAccessOnly
{
    /**
     * Roles allowed to access web application.
     * Collector and Driver should only use mobile app.
     */
    protected array $webAllowedRoles = ['admin', 'customer', 'market-manager'];

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return $next($request);
        }

        $roleSlug = $user->role?->slug;

        if ($roleSlug && !in_array($roleSlug, $this->webAllowedRoles)) {
            auth()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect()->route('login')->withErrors([
                'email' => 'Your account is configured for mobile app access only. Please use the mobile app to login.',
            ]);
        }

        return $next($request);
    }
}
