<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckVendorOwnership
{
    /**
     * Handle an incoming request.
     * Ensures vendors can only access their own products.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        
        // If user is admin, allow access
        if ($user->role->name === 'admin') {
            return $next($request);
        }
        
        // If user is vendor, check product ownership
        if ($user->role->name === 'vendor') {
            $productId = $request->route('product')?->id ?? $request->input('product_id');
            
            if ($productId) {
                $product = \App\Models\Product::find($productId);
                
                if ($product && $product->vendor_id !== $user->vendor->id) {
                    if ($request->expectsJson()) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'You do not have permission to access this product.',
                        ], Response::HTTP_FORBIDDEN);
                    }
                    
                    return redirect()->route('dashboard')
                        ->with('error', 'You do not have permission to access this product.');
                }
            }
        }
        
        return $next($request);
    }
}
