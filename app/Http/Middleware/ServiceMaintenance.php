<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Service;

class ServiceMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            // Only check maintenance for authenticated users
            // Guests can access preview and payment pages
            if (!auth()->check()) {
                return $next($request);
            }

            // Check if we're on a service route
            $route = $request->route();
            
            // Get service from route parameter or URL path
            $service = null;
            
            // First try route parameters
            if ($route && $route->hasParameter('service')) {
                $serviceParam = $route->parameter('service');
                
                // If it's already a Service model instance
                if ($serviceParam instanceof Service) {
                    $service = $serviceParam;
                } else {
                    // Try to find by slug or ID
                    $service = Service::where('slug', $serviceParam)
                        ->orWhere('id', $serviceParam)
                        ->first();
                }
            } elseif ($route && $route->hasParameter('slug')) {
                // Alternative parameter name
                $slug = $route->parameter('slug');
                $service = Service::where('slug', $slug)->first();
            } elseif ($route && $route->hasParameter('slug1')) {
                // Dynamic service routes use slug1 and slug2
                $slug1 = $route->parameter('slug1');
                $slug2 = $route->parameter('slug2');
                
                // Try to find the service by slug
                $service = Service::where('slug', $slug1)->first();
                
                // If not found and there's a slug2, it might be a sub-service
                if (!$service && $slug2) {
                    $service = Service::where('slug', $slug2)->first();
                }
            }
            
            // If we didn't find a service via route params, try parsing the URL
            if (!$service) {
                $path = $request->path();
                // Check for patterns like /services/credit-score-rating/progress/...
                if (preg_match('/^services\/([a-z0-9\-]+)\//', $path, $matches)) {
                    $service = Service::where('slug', $matches[1])->first();
                }
            }

            // If we found a service and it's in maintenance mode
            if ($service && $service->isInMaintenance()) {
            // Check if this is an API request
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'error' => 'maintenance',
                    'message' => $service->getMaintenanceMessage(),
                    'maintenance_ends_at' => $service->maintenance_ends_at?->toIso8601String(),
                ], 503);
            }

            // For web requests, show maintenance page
            return response()->view('errors.service-maintenance', [
                'service' => $service,
                'message' => $service->getMaintenanceMessage(),
                'ends_at' => $service->maintenance_ends_at,
            ], 503);
        }

        return $next($request);
        } catch (\Exception $e) {
            // Log the error but don't block the request
            \Log::error('ServiceMaintenance middleware error: ' . $e->getMessage(), [
                'url' => $request->fullUrl(),
                'user_id' => auth()->id(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Continue with the request even if maintenance check fails
            return $next($request);
        }
    }
}