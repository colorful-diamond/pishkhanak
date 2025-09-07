<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TokenRefreshLogResource\Pages;
use App\Filament\Resources\TokenRefreshLogResource\Widgets;
use App\Models\TokenRefreshLog;
use App\Models\Token;
use App\Jobs\AutomaticTokenRefreshJob;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class TokenRefreshLogResource extends Resource
{
    protected static ?string $model = TokenRefreshLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-clock';

    protected static ?string $navigationGroup = 'Token Management';

    protected static ?int $navigationSort = 3;

    protected static ?string $navigationLabel = 'Token Refresh Logs';

    protected static ?string $pluralLabel = 'Token Refresh Logs';

    protected static ?string $label = 'Token Refresh Log';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Refresh Details')
                    ->schema([
                        Forms\Components\TextInput::make('provider')
                            ->label('Provider')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('token_name')
                            ->label('Token Name')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('status')
                            ->label('Status')
                            ->disabled()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'success' => '✅ Success',
                                'failed' => '❌ Failed',
                                'skipped' => '⏭️ Skipped',
                                default => ucfirst($state),
                            }),
                        
                        Forms\Components\TextInput::make('trigger_type')
                            ->label('Trigger Type')
                            ->disabled()
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'automatic' => '🤖 Automatic',
                                'manual' => '👤 Manual',
                                'forced' => '💪 Forced',
                                default => ucfirst($state),
                            }),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Timing Information')
                    ->schema([
                        Forms\Components\DateTimePicker::make('started_at')
                            ->label('Started At')
                            ->disabled(),
                        
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Completed At')
                            ->disabled(),
                        
                        Forms\Components\TextInput::make('duration_ms')
                            ->label('Duration (ms)')
                            ->disabled()
                            ->formatStateUsing(function ($state) {
                                if (!$state) return 'N/A';
                                return $state < 1000 ? $state . 'ms' : round($state / 1000, 2) . 's';
                            }),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('Results & Messages')
                    ->schema([
                        Forms\Components\Textarea::make('message')
                            ->label('Message')
                            ->disabled()
                            ->rows(2),
                        
                        Forms\Components\TextInput::make('error_code')
                            ->label('Error Code')
                            ->disabled()
                            ->visible(fn ($record) => $record && $record->status === 'failed'),
                        
                        Forms\Components\Textarea::make('error_details')
                            ->label('Error Details')
                            ->disabled()
                            ->rows(3)
                            ->visible(fn ($record) => $record && $record->status === 'failed'),
                    ]),

                Forms\Components\Section::make('Metadata')
                    ->schema([
                        Forms\Components\KeyValue::make('metadata')
                            ->label('Additional Information')
                            ->disabled(),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('provider')
                    ->label('Provider')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        Token::PROVIDER_JIBIT => 'primary',
                        Token::PROVIDER_FINNOTECH => 'success',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'success' => 'success',
                        'failed' => 'danger',
                        'skipped' => 'warning',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'skipped' => 'Skipped',
                        default => ucfirst($state),
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('trigger_type')
                    ->label('Trigger')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'automatic' => 'primary',
                        'manual' => 'info',
                        'forced' => 'warning',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state))
                    ->sortable(),

                Tables\Columns\TextColumn::make('message')
                    ->label('Message')
                    ->limit(50)
                    ->tooltip(function (TokenRefreshLog $record): ?string {
                        return $record->message;
                    })
                    ->wrap(),

                Tables\Columns\TextColumn::make('duration')
                    ->label('Duration')
                    ->getStateUsing(function (TokenRefreshLog $record): ?string {
                        if (!$record->duration_ms) return 'N/A';
                        return $record->duration_ms < 1000 
                            ? $record->duration_ms . 'ms' 
                            : round($record->duration_ms / 1000, 2) . 's';
                    })
                    ->sortable('duration_ms'),

                Tables\Columns\TextColumn::make('started_at')
                    ->label('Started')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Completed')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('provider')
                    ->label('Provider')
                    ->options([
                        Token::PROVIDER_JIBIT => 'Jibit',
                        Token::PROVIDER_FINNOTECH => 'Finnotech',
                    ]),

                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'success' => 'Success',
                        'failed' => 'Failed',
                        'skipped' => 'Skipped',
                    ]),

                Tables\Filters\SelectFilter::make('trigger_type')
                    ->label('Trigger Type')
                    ->options([
                        'automatic' => 'Automatic',
                        'manual' => 'Manual',
                        'forced' => 'Forced',
                    ]),

                Tables\Filters\Filter::make('failed_only')
                    ->query(fn (Builder $query): Builder => $query->where('status', 'failed'))
                    ->label('Failed Only'),

                Tables\Filters\Filter::make('last_24h')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subDay()))
                    ->label('Last 24 Hours'),

                Tables\Filters\Filter::make('last_week')
                    ->query(fn (Builder $query): Builder => $query->where('created_at', '>=', now()->subWeek()))
                    ->label('Last Week'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                
                Tables\Actions\Action::make('retry')
                    ->label('Retry Refresh')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->visible(fn (TokenRefreshLog $record): bool => $record->status === 'failed')
                    ->requiresConfirmation()
                    ->modalHeading('Retry Token Refresh')
                    ->modalDescription('This will trigger a new automatic refresh for this provider.')
                    ->action(function (TokenRefreshLog $record) {
                        try {
                            // Dispatch new refresh job for this provider
                            AutomaticTokenRefreshJob::dispatch($record->provider, true);
                            
                            Notification::make()
                                ->title('Refresh Triggered')
                                ->body("New automatic refresh job dispatched for {$record->provider}")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Failed to retry token refresh: ' . $e->getMessage());
                            
                            Notification::make()
                                ->title('Failed to Trigger Refresh')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->headerActions([
                Tables\Actions\Action::make('trigger_refresh')
                    ->label('Trigger Refresh')
                    ->icon('heroicon-o-play')
                    ->color('primary')
                    ->form([
                        Forms\Components\Select::make('provider')
                            ->label('Provider')
                            ->options([
                                '' => 'All Providers',
                                Token::PROVIDER_JIBIT => 'Jibit Only',
                                Token::PROVIDER_FINNOTECH => 'Finnotech Only',
                            ])
                            ->default(''),
                        
                        Forms\Components\Toggle::make('force')
                            ->label('Force Refresh')
                            ->helperText('Refresh even if tokens don\'t need it yet'),
                    ])
                    ->action(function (array $data) {
                        try {
                            $provider = $data['provider'] ?: null;
                            $force = $data['force'] ?? false;
                            
                            AutomaticTokenRefreshJob::dispatch($provider, $force);
                            
                            $providerText = $provider ? ucfirst($provider) : 'All providers';
                            $forceText = $force ? ' (forced)' : '';
                            
                            Notification::make()
                                ->title('Refresh Triggered')
                                ->body("Automatic refresh job dispatched for {$providerText}{$forceText}")
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Log::error('Failed to trigger token refresh: ' . $e->getMessage());
                            
                            Notification::make()
                                ->title('Failed to Trigger Refresh')
                                ->body('Error: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s'); // Auto-refresh every 30 seconds
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTokenRefreshLogs::route('/'),
            'view' => Pages\ViewTokenRefreshLog::route('/{record}'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            // Widgets will be added later
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $failedCount = static::getModel()::where('status', 'failed')
            ->where('created_at', '>=', now()->subDay())
            ->count();
            
        return $failedCount > 0 ? (string) $failedCount : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $failedCount = static::getModel()::where('status', 'failed')
            ->where('created_at', '>=', now()->subDay())
            ->count();
            
        return $failedCount > 0 ? 'danger' : null;
    }
} 