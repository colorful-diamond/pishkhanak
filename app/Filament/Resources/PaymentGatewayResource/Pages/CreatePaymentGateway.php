<?php

namespace App\Filament\Resources\PaymentGatewayResource\Pages;

use App\Filament\Resources\PaymentGatewayResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreatePaymentGateway extends CreateRecord
{
    protected static string $resource = PaymentGatewayResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Ensure config is properly formatted
        if (isset($data['config']) && is_array($data['config'])) {
            // Remove empty values but keep required fields
            $data['config'] = array_filter($data['config'], function($value, $key) {
                // Keep required fields even if empty for now (validation will handle it)
                if (in_array($key, ['merchant_id', 'username', 'password'])) {
                    return true;
                }
                return $value !== null && $value !== '';
            }, ARRAY_FILTER_USE_BOTH);
        }

        return $data;
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'درگاه پرداخت با موفقیت ایجاد شد';
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('درگاه پرداخت ایجاد شد')
            ->body('درگاه پرداخت جدید با موفقیت ایجاد شد.');
    }
} 