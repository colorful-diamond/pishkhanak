<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TokenResource\Pages;
use App\Models\Token;
use App\Services\TokenService;
use App\Services\FinnotechTokenMapper;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class TokenResource extends Resource
{
    protected static ?string $model = Token::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'Token Management';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('admin.site_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('admin.api_tokens');
    }

    public static function getLabel(): string
    {
        return __('admin.token');
    }

    public static function getPluralLabel(): string
    {
        return __('admin.tokens');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('admin.basic_information'))
                    ->schema([
                        Forms\Components\Select::make('name')
                            ->label(__('admin.token_name'))
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->options([
                                // Jibit tokens
                                Token::NAME_JIBIT => 'Jibit',
                                
                                // Original Finnotech token
                                Token::NAME_FINNOTECH => 'Finnotech (Legacy)',
                                
                                // New Finnotech category tokens
                                Token::NAME_FINNOTECH_INQUIRY => 'Finnotech - Inquiry Services',
                                Token::NAME_FINNOTECH_CREDIT => 'Finnotech - Credit Services',
                                Token::NAME_FINNOTECH_KYC => 'Finnotech - KYC Services',
                                Token::NAME_FINNOTECH_TOKEN => 'Finnotech - Token Services',
                                Token::NAME_FINNOTECH_PROMISSORY => 'Finnotech - Promissory Services',
                                Token::NAME_FINNOTECH_VEHICLE => 'Finnotech - Vehicle Services',
                                Token::NAME_FINNOTECH_INSURANCE => 'Finnotech - Insurance Services',
                                Token::NAME_FINNOTECH_SMS => 'Finnotech - SMS Services',
                            ])
                            ->helperText(__('admin.unique_identifier_for_token'))
                            ->columnSpan(1),

                        Forms\Components\Select::make('provider')
                            ->label(__('admin.provider'))
                            ->required()
                            ->options([
                                Token::PROVIDER_JIBIT => __('admin.jibit'),
                                Token::PROVIDER_FINNOTECH => __('admin.finnotech'),
                            ])
                            ->columnSpan(1),

                        Forms\Components\Toggle::make('is_active')
                            ->label(__('admin.active'))
                            ->required()
                            ->default(true)
                            ->helperText(__('admin.whether_token_is_active'))
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('admin.token_details'))
                    ->schema([
                        Forms\Components\TextInput::make('access_token')
                            ->label(__('admin.access_token'))
                            ->required()
                            ->maxLength(65535)
                            ->helperText(__('admin.access_token_from_api_provider'))
                            ->password()
                            ->revealable()
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('refresh_token')
                            ->label(__('admin.refresh_token'))
                            ->required()
                            ->maxLength(65535)
                            ->helperText(__('admin.refresh_token_from_api_provider'))
                            ->password()
                            ->revealable()
                            ->columnSpanFull(),
                    ])
                    ->columns(1),

                Forms\Components\Section::make(__('admin.expiry_information'))
                    ->schema([
                        Forms\Components\DateTimePicker::make('expires_at')
                            ->label(__('admin.expires_at'))
                            ->helperText(__('admin.when_access_token_expires'))
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('refresh_expires_at')
                            ->label(__('admin.refresh_expires_at'))
                            ->helperText(__('admin.when_refresh_token_expires'))
                            ->columnSpan(1),

                        Forms\Components\DateTimePicker::make('last_used_at')
                            ->label(__('admin.last_used_at'))
                            ->disabled()
                            ->helperText(__('admin.last_time_token_was_used'))
                            ->columnSpan(2),
                    ])
                    ->columns(2),

                Forms\Components\Section::make(__('admin.metadata'))
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label(__('admin.additional_information'))
                            ->helperText(__('admin.additional_metadata_for_token'))
                            ->columnSpan(2),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('admin.token_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            Token::NAME_JIBIT => 'Jibit',
                            Token::NAME_FINNOTECH => 'Finnotech (Legacy)',
                            Token::NAME_FINNOTECH_INQUIRY => 'Inquiry Services',
                            Token::NAME_FINNOTECH_CREDIT => 'Credit Services',
                            Token::NAME_FINNOTECH_KYC => 'KYC Services',
                            Token::NAME_FINNOTECH_TOKEN => 'Token Services',
                            Token::NAME_FINNOTECH_PROMISSORY => 'Promissory Services',
                            Token::NAME_FINNOTECH_VEHICLE => 'Vehicle Services',
                            Token::NAME_FINNOTECH_INSURANCE => 'Insurance Services',
                            Token::NAME_FINNOTECH_SMS => 'SMS Services',
                            default => $state,
                        };
                    }),

                Tables\Columns\BadgeColumn::make('provider')
                    ->label(__('admin.provider'))
                    ->colors([
                        'primary' => Token::PROVIDER_JIBIT,
                        'success' => Token::PROVIDER_FINNOTECH,
                    ])
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            Token::PROVIDER_JIBIT => __('admin.jibit'),
                            Token::PROVIDER_FINNOTECH => __('admin.finnotech'),
                            default => $state,
                        };
                    })
                    ->searchable(),

                Tables\Columns\BadgeColumn::make('category')
                    ->label('Category')
                    ->getStateUsing(function (Token $record): string {
                        if (FinnotechTokenMapper::isFinnotechCategoryToken($record->name)) {
                            return str_replace('fino_', '', $record->name);
                        }
                        return match ($record->name) {
                            Token::NAME_JIBIT => 'main',
                            Token::NAME_FINNOTECH => 'legacy',
                            default => 'other',
                        };
                    })
                    ->colors([
                        'primary' => 'main',
                        'secondary' => 'legacy',
                        'success' => 'inquiry',
                        'info' => 'credit',
                        'warning' => 'kyc',
                        'danger' => 'vehicle',
                    ])
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->label(__('admin.active'))
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label(__('admin.token_status'))
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
                    ->colors([
                        'success' => 'healthy',
                        'warning' => 'needs_refresh',
                        'danger' => 'expired',
                        'secondary' => 'inactive',
                    ]),

                Tables\Columns\TextColumn::make('expires_at')
                    ->dateTime()
                    ->sortable()
                    ->label(__('admin.access_expires'))
                    ->formatStateUsing(function ($state) {
                        if (!$state) return 'هرگز';
                        return $state->format('Y-m-d H:i') . ' (' . $state->diffForHumans() . ')';
                    }),

                Tables\Columns\TextColumn::make('last_used_at')
                    ->dateTime()
                    ->sortable()
                    ->label(__('admin.last_used'))
                    ->formatStateUsing(function ($state) {
                        return $state ? $state->diffForHumans() : 'هرگز';
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('admin.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('admin.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider')
                    ->label(__('admin.provider'))
                    ->options([
                        Token::PROVIDER_JIBIT => __('admin.jibit'),
                        Token::PROVIDER_FINNOTECH => __('admin.finnotech'),
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label(__('admin.active_status')),

                Tables\Filters\Filter::make('expired')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', now()))
                    ->label(__('admin.expired_tokens')),

                Tables\Filters\Filter::make('needs_refresh')
                    ->query(fn (Builder $query): Builder => $query->where('expires_at', '<', now()->addMinutes(5)))
                    ->label(__('admin.tokens_need_refresh')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('refresh')
                    ->label(__('admin.refresh_token'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(__('admin.refresh_token'))
                    ->modalDescription(__('admin.attempt_to_refresh_token'))
                    ->action(function (Token $record) {
                        try {
                            $tokenService = app(TokenService::class);
                            
                            // Use token name for category-specific tokens
                            if (FinnotechTokenMapper::isFinnotechCategoryToken($record->name)) {
                                $success = $tokenService->refreshTokenByName($record->name);
                            } else {
                                $success = $tokenService->refreshToken($record->provider);
                            }
                            
                            if ($success) {
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

                Tables\Actions\Action::make('test')
                    ->label(__('admin.test_token'))
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->color('info')
                    ->action(function (Token $record) {
                        try {
                            $tokenService = app(TokenService::class);
                            
                            // Use token name for category-specific tokens
                            if (FinnotechTokenMapper::isFinnotechCategoryToken($record->name)) {
                                $isValid = $tokenService->ensureValidToken($record->provider, $record->name);
                            } else {
                                $isValid = $tokenService->ensureValidToken($record->provider);
                            }
                            
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

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    
                    Tables\Actions\BulkAction::make('refresh_selected')
                        ->label(__('admin.refresh_selected'))
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $tokenService = app(TokenService::class);
                            $successCount = 0;
                            $failCount = 0;
                            
                            foreach ($records as $record) {
                                try {
                                    $success = $tokenService->refreshToken($record->provider);
                                    if ($success) {
                                        $successCount++;
                                    } else {
                                        $failCount++;
                                    }
                                } catch (\Exception $e) {
                                    $failCount++;
                                    Log::error("Bulk refresh failed for {$record->provider}: " . $e->getMessage());
                                }
                            }
                            
                            Notification::make()
                                ->title(__('admin.bulk_refresh_completed'))
                                ->body("با موفقیت تازه‌سازی شد: {$successCount}، ناموفق: {$failCount}")
                                ->success()
                                ->send();
                        }),

                    Tables\Actions\BulkAction::make('deactivate_selected')
                        ->label(__('admin.deactivate_selected'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $count = 0;
                            foreach ($records as $record) {
                                $record->deactivate();
                                $count++;
                            }
                            
                            Notification::make()
                                ->title(__('admin.tokens_deactivated'))
                                ->body("{$count} توکن غیرفعال شد.")
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->headerActions([
                Tables\Actions\Action::make('generate_missing_categories')
                    ->label('Generate Missing Category Tokens')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Generate Missing Finnotech Category Tokens')
                    ->modalDescription('This will generate tokens for all missing Finnotech categories.')
                    ->action(function () {
                        try {
                            $tokenService = app(TokenService::class);
                            $results = $tokenService->generateMissingFinnotechCategoryTokens();
                            
                            $successCount = array_sum($results);
                            $totalCount = count($results);
                            
                            $failedCategories = array_keys(array_filter($results, fn($result) => !$result));
                            
                            if ($successCount === $totalCount) {
                                Notification::make()
                                    ->title('All Category Tokens Generated')
                                    ->body("Successfully generated {$successCount} category tokens.")
                                    ->success()
                                    ->send();
                            } else {
                                $message = "Generated {$successCount}/{$totalCount} tokens.";
                                if (!empty($failedCategories)) {
                                    $message .= " Failed: " . implode(', ', $failedCategories);
                                }
                                
                                Notification::make()
                                    ->title('Partial Success')
                                    ->body($message)
                                    ->warning()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Log::error('Generate missing category tokens failed: ' . $e->getMessage());
                            
                            Notification::make()
                                ->title('Generation Failed')
                                ->body('خطایی رخ داده است: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('refresh_all')
                    ->label(__('admin.refresh_all_tokens'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading(__('admin.refresh_all_tokens'))
                    ->modalDescription(__('admin.attempt_to_refresh_all_tokens'))
                    ->action(function () {
                        try {
                            $tokenService = app(TokenService::class);
                            $results = $tokenService->refreshAllTokensNeedingRefresh();
                            $successCount = array_sum($results);
                            $totalCount = count($results);
                            
                            Notification::make()
                                ->title(__('admin.bulk_refresh_completed'))
                                ->body("با موفقیت {$successCount} از {$totalCount} ارائه‌دهنده تازه‌سازی شد.")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Bulk token refresh failed: ' . $e->getMessage());
                            
                            Notification::make()
                                ->title('تازه‌سازی دسته‌ای ناموفق بود')
                                ->body('خطایی رخ داده است: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\Action::make('health_check')
                    ->label(__('admin.health_check'))
                    ->icon('heroicon-o-heart')
                    ->color('info')
                    ->action(function () {
                        $tokenService = app(TokenService::class);
                        $healthStatus = $tokenService->getTokenHealthStatus();
                        
                        $healthy = 0;
                        $total = count($healthStatus);
                        
                        foreach ($healthStatus as $status) {
                            if ($status['active'] && !$status['access_token_expired']) {
                                $healthy++;
                            }
                        }
                        
                        Notification::make()
                            ->title(__('admin.token_health_check'))
                            ->body("توکن‌های سالم: {$healthy}/{$total}")
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTokens::route('/'),
            'create' => Pages\CreateToken::route('/create'),
            'view' => Pages\ViewToken::route('/{record}'),
            'edit' => Pages\EditToken::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        try {
            $expiredCount = static::getModel()::where('is_active', true)
                ->where('expires_at', '<', now())
                ->count();
                
            return $expiredCount > 0 ? (string) $expiredCount : null;
        } catch (\Exception $e) {
            // Return null if table doesn't exist (before migration)
            return null;
        }
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        try {
            $expiredCount = static::getModel()::where('is_active', true)
                ->where('expires_at', '<', now())
                ->count();
                
            return $expiredCount > 0 ? 'danger' : 'primary';
        } catch (\Exception $e) {
            // Return null if table doesn't exist (before migration)
            return null;
        }
    }
} 