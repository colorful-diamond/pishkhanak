<?php

namespace App\Filament\Resources\TokenResource\Pages;

use App\Filament\Resources\TokenResource;
use App\Models\Token;
use App\Services\TokenService;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Support\Facades\Log;

class ViewToken extends ViewRecord
{
    protected static string $resource = TokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
            
            Actions\Action::make('refresh')
                ->label(__('admin.refresh_token'))
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading(__('admin.refresh_token'))
                ->modalDescription(__('admin.attempt_to_refresh_token'))
                ->action(function (Token $record) {
                    try {
                        $tokenService = app(TokenService::class);
                        $success = $tokenService->refreshToken($record->provider);
                        
                        if ($success) {
                            // Refresh the page data
                            $this->refreshFormData([$record->getKey()]);
                            
                            Notification::make()
                                ->title(__('admin.token_refreshed_successfully'))
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

            Actions\Action::make('test')
                ->label(__('admin.test_token'))
                ->icon('heroicon-o-wrench-screwdriver')
                ->color('info')
                ->action(function (Token $record) {
                    try {
                        $tokenService = app(TokenService::class);
                        $isValid = $tokenService->ensureValidToken($record->provider);
                        
                        if ($isValid) {
                            Notification::make()
                                ->title(__('admin.token_test_successful'))
                                ->body('توکن معتبر است و به درستی کار می‌کند.')
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title(__('admin.token_test_failed'))
                                ->body('توکن معتبر نیست یا قابل استفاده نمی‌باشد.')
                                ->warning()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Notification::make()
                            ->title('خطا در تست توکن')
                            ->body('خطایی رخ داده است: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),

            Actions\Action::make('clear_cache')
                ->label(__('admin.clear_cache'))
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading(__('admin.clear_cache'))
                ->modalDescription('این کار کش مربوط به توکن را پاک خواهد کرد.')
                ->action(function (Token $record) {
                    $record->clearCache();
                    
                    Notification::make()
                        ->title(__('admin.cache_cleared'))
                        ->body('کش توکن پاک شده است.')
                        ->success()
                        ->send();
                }),

            Actions\DeleteAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make(__('admin.basic_information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('name')
                            ->label(__('admin.token_name'))
                            ->weight('bold'),

                        Infolists\Components\TextEntry::make('provider')
                            ->label(__('admin.provider'))
                            ->badge()
                            ->formatStateUsing(function (string $state): string {
                                return match ($state) {
                                    Token::PROVIDER_JIBIT => __('admin.jibit'),
                                    Token::PROVIDER_FINNOTECH => __('admin.finnotech'),
                                    default => $state,
                                };
                            })
                            ->color(fn (string $state): string => match ($state) {
                                Token::PROVIDER_JIBIT => 'primary',
                                Token::PROVIDER_FINNOTECH => 'success',
                                default => 'gray',
                            }),

                        Infolists\Components\IconEntry::make('is_active')
                            ->label(__('admin.active_status'))
                            ->boolean(),

                        Infolists\Components\TextEntry::make('status')
                            ->label(__('admin.token_health'))
                            ->getStateUsing(function (Token $record): string {
                                if (!$record->is_active) {
                                    return 'inactive';
                                }
                                if ($record->isAccessTokenExpired()) {
                                    return 'expired';
                                }
                                if ($record->needsRefresh()) {
                                    return 'needs_refresh';
                                }
                                return 'healthy';
                            })
                            ->formatStateUsing(function (string $state): string {
                                return match ($state) {
                                    'healthy' => __('admin.healthy'),
                                    'needs_refresh' => __('admin.needs_refresh'),
                                    'expired' => __('admin.expired'),
                                    'inactive' => __('admin.inactive'),
                                    default => $state,
                                };
                            })
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'healthy' => 'success',
                                'needs_refresh' => 'warning',
                                'expired' => 'danger',
                                'inactive' => 'gray',
                                default => 'gray',
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('admin.token_details'))
                    ->schema([
                        Infolists\Components\TextEntry::make('access_token')
                            ->label(__('admin.access_token'))
                            ->copyable()
                            ->copyMessage('توکن دسترسی کپی شد!')
                            ->formatStateUsing(fn (string $state): string => '••••••••••••' . substr($state, -8))
                            ->helperText(__('admin.click_to_copy_full_token')),

                        Infolists\Components\TextEntry::make('refresh_token')
                            ->label(__('admin.refresh_token'))
                            ->copyable()
                            ->copyMessage('توکن تازه‌سازی کپی شد!')
                            ->formatStateUsing(fn (string $state): string => '••••••••••••' . substr($state, -8))
                            ->helperText(__('admin.click_to_copy_full_token')),
                    ])
                    ->columns(1),

                Infolists\Components\Section::make(__('admin.expiry_information'))
                    ->schema([
                        Infolists\Components\TextEntry::make('expires_at')
                            ->label(__('admin.expires_at'))
                            ->dateTime()
                            ->formatStateUsing(function ($state) {
                                if (!$state) return 'هرگز';
                                return $state->format('Y-m-d H:i:s') . ' (' . $state->diffForHumans() . ')';
                            }),

                        Infolists\Components\TextEntry::make('refresh_expires_at')
                            ->label(__('admin.refresh_expires_at'))
                            ->dateTime()
                            ->formatStateUsing(function ($state) {
                                if (!$state) return 'هرگز';
                                return $state->format('Y-m-d H:i:s') . ' (' . $state->diffForHumans() . ')';
                            }),

                        Infolists\Components\TextEntry::make('last_used_at')
                            ->label(__('admin.last_used_at'))
                            ->dateTime()
                            ->formatStateUsing(function ($state) {
                                return $state ? $state->format('Y-m-d H:i:s') . ' (' . $state->diffForHumans() . ')' : 'هرگز';
                            }),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label(__('admin.created_at'))
                            ->dateTime()
                            ->formatStateUsing(function ($state) {
                                return $state->format('Y-m-d H:i:s') . ' (' . $state->diffForHumans() . ')';
                            }),
                    ])
                    ->columns(2),

                Infolists\Components\Section::make(__('admin.metadata'))
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('metadata')
                            ->label(__('admin.additional_information'))
                            ->hiddenLabel(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
} 