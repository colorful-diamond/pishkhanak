<?php

namespace App\Providers;

use App\Services\GeminiAutoResponseService;
use App\Services\GeminiService;
use Illuminate\Support\ServiceProvider;

class AutoResponseServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register the Gemini Auto Response Service as a singleton
        $this->app->singleton(GeminiAutoResponseService::class, function ($app) {
            return new GeminiAutoResponseService($app->make(GeminiService::class));
        });

        // Register alias for easier access
        $this->app->alias(GeminiAutoResponseService::class, 'gemini.autoresponse');
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish config file
        $this->publishes([
            __DIR__.'/../../config/gemini.php' => config_path('gemini.php'),
        ], 'gemini-config');
    }
}
