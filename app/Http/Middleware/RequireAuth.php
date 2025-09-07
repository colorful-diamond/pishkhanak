<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RequireAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Define auth-related routes that should be accessible without authentication
        $authRoutes = [
            'app.auth.login',
            'app.auth.login.submit',
            'app.auth.register',
            'app.auth.register.submit',
            'app.auth.send-otp',
            'app.auth.verify-otp',
            'app.password.request',
            'app.password.email',
            'app.password.reset',
            'app.password.update',
        ];

        // Check if the current route is an auth route
        $currentRouteName = $request->route()->getName();
        $isAuthRoute = in_array($currentRouteName, $authRoutes);

        // If it's an auth route, allow access
        if ($isAuthRoute) {
            return $next($request);
        }

        // For non-auth routes, require authentication
        if (!Auth::check()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'لطفاً ابتدا وارد شوید',
                    'redirect' => route('app.auth.login')
                ], 401);
            }

            return redirect()->route('app.auth.login')->with('error', 'لطفاً ابتدا وارد شوید');
        }

        return $next($request);
    }
} 