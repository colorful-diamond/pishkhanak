<?php

namespace App\Providers;

use App\Services\Telegram\Contracts\TelegramApiClientInterface;
use App\Services\Telegram\Contracts\TicketRepositoryInterface;
use App\Services\Telegram\Core\TelegramApiClient;
use App\Services\Telegram\Core\MessageRouter;
use App\Services\Telegram\Core\WebhookProcessor;
use App\Services\Telegram\Core\AdminAuthService;
use App\Services\Telegram\Core\AuditLogger;
use App\Services\Telegram\Handlers\GeneralCommandHandler;
use App\Services\Telegram\Handlers\TicketCommandHandler;
use App\Services\Telegram\Handlers\AdminCommandHandler;
use App\Services\Telegram\Core\PersianTextProcessor;
use App\Services\Telegram\Repositories\TicketRepository;
use App\Services\PersianTextValidator;
use App\Services\TelegramAdminAuth;
use Illuminate\Support\ServiceProvider;

/**
 * Telegram Service Provider
 * 
 * Registers all Telegram-related services and their dependencies
 * Configures the clean architecture dependency injection
 */
class TelegramServiceProvider extends ServiceProvider
{
    /**
     * Register services
     */
    public function register(): void
    {
        // Register core contracts
        $this->app->bind(TelegramApiClientInterface::class, function ($app) {
            $botToken = env('TELEGRAM_BOT_TOKEN');
            
            if (empty($botToken)) {
                throw new \InvalidArgumentException(
                    'Telegram bot token is not configured. Please set TELEGRAM_BOT_TOKEN in your .env file.'
                );
            }
            
            return new TelegramApiClient(
                botToken: $botToken,
                proxyConfig: [
                    'enabled' => true,
                    'type' => 'http',
                    'host' => '127.0.0.1',
                    'port' => 1090
                ]
            );
        });

        $this->app->bind(TicketRepositoryInterface::class, TicketRepository::class);

        // Register admin authentication services
        $this->app->singleton(AdminAuthService::class);
        $this->app->singleton(AuditLogger::class);
        
        // Register Persian text processor
        $this->app->singleton(PersianTextProcessor::class);

        // Register the webhook processor
        $this->app->singleton(WebhookProcessor::class, function ($app) {
            return new WebhookProcessor(
                router: $app->make(MessageRouter::class),
                textValidator: $app->make(PersianTextValidator::class),
                adminAuth: $app->make(TelegramAdminAuth::class)
            );
        });

        // Register the message router
        $this->app->singleton(MessageRouter::class, function ($app) {
            $router = new MessageRouter(
                messageHandler: $app->make(\App\Services\Telegram\Handlers\MessageHandler::class),
                callbackQueryHandler: $app->make(\App\Services\Telegram\Handlers\CallbackQueryHandler::class),
                inlineQueryHandler: $app->make(\App\Services\Telegram\Handlers\InlineQueryHandler::class)
            );

            // Register command handlers
            $router->registerCommand('start', $app->make(GeneralCommandHandler::class));
            $router->registerCommand('help', $app->make(GeneralCommandHandler::class));
            $router->registerCommand('about', $app->make(GeneralCommandHandler::class));
            $router->registerCommand('راهنما', $app->make(GeneralCommandHandler::class));
            $router->registerCommand('درباره', $app->make(GeneralCommandHandler::class));
            
            $router->registerCommand('tickets', $app->make(TicketCommandHandler::class));
            $router->registerCommand('ticket', $app->make(TicketCommandHandler::class));
            $router->registerCommand('تیکت', $app->make(TicketCommandHandler::class));
            $router->registerCommand('تیکت‌ها', $app->make(TicketCommandHandler::class));

            // Admin commands
            $router->registerCommand('admin', $app->make(AdminCommandHandler::class));
            $router->registerCommand('stats', $app->make(AdminCommandHandler::class));
            $router->registerCommand('users', $app->make(AdminCommandHandler::class));
            $router->registerCommand('broadcast', $app->make(AdminCommandHandler::class));
            $router->registerCommand('tickets_admin', $app->make(AdminCommandHandler::class));
            $router->registerCommand('system', $app->make(AdminCommandHandler::class));
            $router->registerCommand('مدیریت', $app->make(AdminCommandHandler::class));

            return $router;
        });

        // Register command handlers
        $this->app->bind(GeneralCommandHandler::class, function ($app) {
            return new GeneralCommandHandler(
                apiClient: $app->make(TelegramApiClientInterface::class)
            );
        });

        $this->app->bind(TicketCommandHandler::class, function ($app) {
            return new TicketCommandHandler(
                apiClient: $app->make(TelegramApiClientInterface::class),
                ticketRepository: $app->make(TicketRepositoryInterface::class)
            );
        });

        $this->app->bind(AdminCommandHandler::class, function ($app) {
            return new AdminCommandHandler(
                apiClient: $app->make(TelegramApiClientInterface::class),
                ticketRepository: $app->make(TicketRepositoryInterface::class),
                textProcessor: $app->make(PersianTextProcessor::class)
            );
        });

        // Placeholder handlers (to be implemented)
        $this->app->bind(\App\Services\Telegram\Handlers\MessageHandler::class, function ($app) {
            return new \App\Services\Telegram\Handlers\MessageHandler(
                apiClient: $app->make(TelegramApiClientInterface::class)
            );
        });

        $this->app->bind(\App\Services\Telegram\Handlers\CallbackQueryHandler::class, function ($app) {
            return new \App\Services\Telegram\Handlers\CallbackQueryHandler(
                apiClient: $app->make(TelegramApiClientInterface::class)
            );
        });

        $this->app->bind(\App\Services\Telegram\Handlers\InlineQueryHandler::class, function ($app) {
            return new \App\Services\Telegram\Handlers\InlineQueryHandler(
                apiClient: $app->make(TelegramApiClientInterface::class)
            );
        });

        // Register existing services if they don't already exist
        $this->app->singleton(PersianTextValidator::class);
        $this->app->singleton(TelegramAdminAuth::class);
    }

    /**
     * Bootstrap services
     */
    public function boot(): void
    {
        // Publish configuration if needed
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../../config/telegram.php' => config_path('telegram.php'),
            ], 'telegram-config');
        }
    }

    /**
     * Get the services provided by the provider
     */
    public function provides(): array
    {
        return [
            TelegramApiClientInterface::class,
            TicketRepositoryInterface::class,
            WebhookProcessor::class,
            MessageRouter::class,
            GeneralCommandHandler::class,
            TicketCommandHandler::class,
        ];
    }
}