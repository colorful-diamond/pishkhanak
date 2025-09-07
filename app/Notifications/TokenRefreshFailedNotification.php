<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

class TokenRefreshFailedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected array $failedProviders;

    /**
     * Create a new notification instance.
     */
    public function __construct(array $failedProviders)
    {
        $this->failedProviders = $failedProviders;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $providerNames = collect($this->failedProviders)
            ->pluck('provider')
            ->map(fn($provider) => ucfirst($provider))
            ->join(', ');

        $mailMessage = (new MailMessage)
            ->subject('🚨 API Token Refresh Failed - ' . config('app.name'))
            ->greeting('Hello Administrator,')
            ->line('The automatic API token refresh has failed for the following providers:')
            ->line('**Failed Providers:** ' . $providerNames);

        // Add details for each failed provider
        foreach ($this->failedProviders as $provider) {
            $mailMessage->line("**{$provider['provider']}:** {$provider['error']}");
        }

        $mailMessage
            ->line('Please check the access panel and logs for more details.')
            ->action('View Token Logs', URL::to('/access/token-refresh-logs'))
            ->line('This issue requires immediate attention to ensure API services continue to work properly.')
            ->salutation('Best regards,<br>' . config('app.name') . ' System');

        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'failed_providers' => $this->failedProviders,
            'failed_count' => count($this->failedProviders),
            'timestamp' => now()->toISOString(),
        ];
    }

    /**
     * Get the notification's channels.
     */
    public function broadcastOn(): array
    {
        return [];
    }
} 