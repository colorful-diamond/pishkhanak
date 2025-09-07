<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use App\Livewire\ContentCreation;
use Illuminate\Support\Facades\Event;
use App\Services\ServiceFormAnalyzer;
use App\Services\ConversationManager;
use App\Services\IntentClassifier;
use App\Services\SmartValidator;
use App\Services\ServiceUrlGenerator;
use App\Services\GeminiService;
use App\Services\AiChatService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register AI Chat Services
        $this->app->singleton(ServiceFormAnalyzer::class);
        
        $this->app->singleton(ConversationManager::class, function ($app) {
            return new ConversationManager();
        });
        
        $this->app->singleton(IntentClassifier::class, function ($app) {
            return new IntentClassifier($app->make(GeminiService::class));
        });
        
        $this->app->singleton(SmartValidator::class, function ($app) {
            return new SmartValidator(
                $app->make(GeminiService::class),
                $app->make(ServiceFormAnalyzer::class)
            );
        });
        
        $this->app->singleton(ServiceUrlGenerator::class, function ($app) {
            return new ServiceUrlGenerator(
                $app->make(ConversationManager::class),
                $app->make(SmartValidator::class)
            );
        });
        
        // Update AiChatService to use all dependencies
        $this->app->singleton(AiChatService::class, function ($app) {
            return new AiChatService(
                $app->make(GeminiService::class),
                $app->make(ServiceFormAnalyzer::class),
                $app->make(ConversationManager::class),
                $app->make(IntentClassifier::class),
                $app->make(SmartValidator::class),
                $app->make(ServiceUrlGenerator::class)
            );
        });
    }

    public function boot(): void
    {
        Event::listen(function (\SocialiteProviders\Manager\SocialiteWasCalled $event) {
            $event->extendSocialite('discord', \SocialiteProviders\Discord\Provider::class);
        });

        // Register custom SMS notification channel
        \Illuminate\Support\Facades\Notification::extend('sms', function ($app) {
            return new \App\Channels\SmsChannel($app->make(\App\Services\SmsService::class));
        });
        // $cachedIp = cache()->remember('drweb_ip', 1800, function () {
        //     return gethostbyname('drweb.asuscomm.com');
        // });


        // if (request()->ip() === $cachedIp) {
        //     config(['app.debug' => true]);
        // }

    }
}