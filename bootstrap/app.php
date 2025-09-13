<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckFinnotechSmsAuth;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\AuthRateLimit;
use App\Http\Middleware\InputValidation;
use App\Http\Middleware\AuditLog;
use App\Http\Middleware\TelegramWebhookAuth;
use App\Http\Middleware\TelegramRateLimit;
use App\Http\Middleware\ServiceMaintenance;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/telegram.php'));
        },
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['prefix' => '', 'middleware' => ['web', 'auth']],
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register custom middleware
        $middleware->alias([
            'finnotech.sms.auth' => CheckFinnotechSmsAuth::class,
            'require.auth' => \App\Http\Middleware\RequireAuth::class,
            'ai-search-throttle' => \App\Http\Middleware\AiSearchThrottle::class,
            'file-upload-security' => \App\Http\Middleware\FileUploadSecurityMiddleware::class,
            'redirect' => \App\Http\Middleware\RedirectMiddleware::class,
            'security.headers' => SecurityHeaders::class,
            'auth.rate.limit' => AuthRateLimit::class,
            'input.validation' => InputValidation::class,
            'audit.log' => AuditLog::class,
            'telegram.webhook.auth' => TelegramWebhookAuth::class,
            'telegram.rate.limit' => TelegramRateLimit::class,
            'service.maintenance' => ServiceMaintenance::class,
        ]);
        
        // Add redirect middleware to web group (early in the stack)
        $middleware->web(prepend: [
            \App\Http\Middleware\RedirectMiddleware::class,
        ]);
        
        // Add security headers, input validation, audit logging, and service maintenance middleware to web group
        $middleware->web(append: [
            SecurityHeaders::class,
            InputValidation::class,
            AuditLog::class,
            ServiceMaintenance::class,
        ]);
        
        // Replace the default VerifyCsrfToken with our custom one
        $middleware->web(replace: [
            \Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class => \App\Http\Middleware\DisableCsrfForPaymentCallbacks::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
