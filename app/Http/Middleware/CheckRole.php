<?php
/**
 * Command to create middleware:
 * php artisan make:middleware CheckRole
 */

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // Check if user is logged in
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Get the authenticated user
        $user = Auth::user();
        
        // Get the role name of the user
        $userRole = $user->role->name ?? null;
        
        // Check if user has any of the allowed roles
        if (in_array($userRole, $roles)) {
            return $next($request);
        }
        
        // Redirect or return a response based on request type
        if ($request->expectsJson()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized access',
            ], Response::HTTP_FORBIDDEN);
        }
        
        return redirect()->route('dashboard')->with('error', 'You do not have permission to access this page.');
    }
}