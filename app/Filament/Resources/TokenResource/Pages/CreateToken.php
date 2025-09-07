<?php

namespace App\Filament\Resources\TokenResource\Pages;

use App\Filament\Resources\TokenResource;
use App\Models\Token;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class CreateToken extends CreateRecord
{
    protected static string $resource = TokenResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set default expiry times if not provided
        if (!isset($data['expires_at']) || !$data['expires_at']) {
            $data['expires_at'] = now()->addHours(24);
        }

        if (!isset($data['refresh_expires_at']) || !$data['refresh_expires_at']) {
            $data['refresh_expires_at'] = now()->addHours(48);
        }

        // Set last_used_at to null for new tokens
        $data['last_used_at'] = null;

        return $data;
    }

    protected function afterCreate(): void
    {
        // Clear token cache after creating new token
        Token::clearAllCache();

        Notification::make()
            ->title(__('admin.token_created_successfully'))
            ->body('توکن API ایجاد و در کش ذخیره شد.')
            ->success()
            ->send();
    }
} 