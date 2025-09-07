<?php

namespace App\Filament\Resources\TokenResource\Pages;

use App\Filament\Resources\TokenResource;
use App\Models\Token;
use App\Services\TokenService;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class EditToken extends EditRecord
{
    protected static string $resource = TokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            
            Actions\Action::make('refresh')
                ->label(__('admin.refresh_token'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('admin.refresh_token'))
                ->modalDescription(__('admin.update_form_with_new_token_values'))
                ->action(function () {
                    try {
                        $tokenService = app(TokenService::class);
                        $success = $tokenService->refreshToken($this->record->provider);
                        
                        if ($success) {
                            // Refresh the record from database
                            $this->record->refresh();
                            
                            // Update form data
                            $this->fillForm();
                            
                            Notification::make()
                                ->title(__('admin.token_refreshed_successfully'))
                                ->body('فرم با مقادیر جدید توکن به‌روزرسانی شد.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title(__('admin.token_refresh_failed'))
                                ->body('لطفاً لاگ‌ها را برای جزئیات بیشتر بررسی کنید.')
                                ->danger()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Log::error('Manual token refresh failed: ' . $e->getMessage());
                        
                        Notification::make()
                            ->title('خطا در تازه‌سازی توکن')
                            ->body('خطایی رخ داده است: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function afterSave(): void
    {
        // Clear token cache after saving
        Token::clearAllCache();

        Notification::make()
            ->title(__('admin.token_updated_successfully'))
            ->body('توکن API به‌روزرسانی شده و کش پاک شده است.')
            ->success()
            ->send();
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If tokens are being manually updated, clear last_used_at to reset usage tracking
        if (isset($data['access_token']) && $data['access_token'] !== $this->record->access_token) {
            $data['last_used_at'] = null;
        }

        return $data;
    }
} 