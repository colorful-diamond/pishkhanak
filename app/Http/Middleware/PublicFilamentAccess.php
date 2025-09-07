<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Filament\Facades\Filament;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Database\Eloquent\Model;

class PublicFilamentAccess extends Middleware
{
    public function handle($request, Closure $next, ...$guards)
    {
        // Bypass authentication if the request matches your specific route(s)

        filament()->getCurrentPanel()->sidebarWidth(auth()->check() ? '20rem' : '0rem');

        if ($request->getPathInfo() === '/app/service' || $request->getPathInfo() === '/app/result') {
            return $next($request); 
        }else{
            $this->authenticate($request, $guards);
        }
        // Otherwise, proceed with normal authentication checks
        return $next($request);
    }

    protected function authenticate($request, array $guards): void
    {
        $guard = Filament::auth();

        if (! $guard->check()) {
            $this->unauthenticated($request, $guards);

            return;
        }

        $this->auth->shouldUse(Filament::getAuthGuard());

        /** @var Model $user */
        $user = $guard->user();

        $panel = Filament::getCurrentPanel();

        abort_if(
            $user instanceof FilamentUser ?
                (! $user->canAccessPanel($panel)) :
                (config('app.env') !== 'local'),
            403,
        );
    }

    protected function redirectTo($request): ?string
    {
        return Filament::getLoginUrl();
    }
}