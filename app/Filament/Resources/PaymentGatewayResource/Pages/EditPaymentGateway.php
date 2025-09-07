<?php

namespace App\Filament\Resources\PaymentGatewayResource\Pages;

use App\Filament\Resources\PaymentGatewayResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPaymentGateway extends EditRecord
{
    protected static string $resource = PaymentGatewayResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('حذف درگاه'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
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
        return 'درگاه پرداخت با موفقیت بروزرسانی شد';
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('درگاه پرداخت بروزرسانی شد')
            ->body('تغییرات با موفقیت ذخیره شدند.');
    }
} 