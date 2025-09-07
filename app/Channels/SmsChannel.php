<?php

namespace App\Channels;

use App\Services\SmsService;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    protected SmsService $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    /**
     * Send the given notification.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        try {
            // Check if notifiable has mobile number
            if (!$notifiable->mobile) {
                Log::warning('SMS notification skipped - no mobile number', [
                    'notifiable_type' => get_class($notifiable),
                    'notifiable_id' => $notifiable->id ?? null,
                    'notification_type' => get_class($notification)
                ]);
                return;
            }

            // Get SMS message from notification
            if (!method_exists($notification, 'toSms')) {
                Log::warning('SMS notification skipped - no toSms method', [
                    'notification_type' => get_class($notification)
                ]);
                return;
            }

            $message = $notification->toSms($notifiable);

            if (empty($message)) {
                Log::warning('SMS notification skipped - empty message', [
                    'notifiable_type' => get_class($notifiable),
                    'notifiable_id' => $notifiable->id ?? null,
                    'notification_type' => get_class($notification)
                ]);
                return;
            }

            // Send SMS using the SMS service
            $result = $this->smsService->sendSms($notifiable->mobile, $message);

            if ($result['success']) {
                Log::info('SMS notification sent successfully', [
                    'notifiable_type' => get_class($notifiable),
                    'notifiable_id' => $notifiable->id ?? null,
                    'mobile' => $notifiable->mobile,
                    'notification_type' => get_class($notification),
                    'message_length' => strlen($message)
                ]);
            } else {
                Log::error('SMS notification failed', [
                    'notifiable_type' => get_class($notifiable),
                    'notifiable_id' => $notifiable->id ?? null,
                    'mobile' => $notifiable->mobile,
                    'notification_type' => get_class($notification),
                    'error' => $result['message'] ?? 'Unknown error'
                ]);
            }

        } catch (\Exception $e) {
            Log::error('SMS notification exception', [
                'notifiable_type' => get_class($notifiable),
                'notifiable_id' => $notifiable->id ?? null,
                'notification_type' => get_class($notification),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 