<?php

namespace App\Filament\Resources\TokenResource\Widgets;

use App\Models\Token;
use App\Services\TokenService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class TokenHealthWidget extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'وضعیت سلامت توکن‌ها';

    protected static ?int $sort = 2;

    private $healthData;

    public function table(Table $table): Table
    {
        $tokenService = app(TokenService::class);
        $healthStatus = $tokenService->getTokenHealthStatus();

        // Convert health status to collection for table display
        $this->healthData = collect([
            [
                'provider' => 'Jibit',
                'provider_key' => Token::PROVIDER_JIBIT,
                'status' => $healthStatus[Token::PROVIDER_JIBIT] ?? null,
            ],
            [
                'provider' => 'Finnotech',
                'provider_key' => Token::PROVIDER_FINNOTECH,
                'status' => $healthStatus[Token::PROVIDER_FINNOTECH] ?? null,
            ]
        ]);

        return $table
            ->query(
                // Use Token model for compatibility
                Token::query()->where('id', 0)
            )
            ->columns([
                Tables\Columns\TextColumn::make('provider')
                    ->label(__('admin.provider'))
                    ->weight('bold')
                    ->getStateUsing(function ($record, $rowLoop): string {
                        $data = $this->healthData->get($rowLoop->index);
                        return $data['provider'] ?? 'نامشخص';
                    }),

                Tables\Columns\BadgeColumn::make('active_status')
                    ->label(__('admin.active'))
                    ->getStateUsing(function ($record, $rowLoop): string {
                        $data = $this->healthData->get($rowLoop->index);
                        $status = $data['status'] ?? null;
                        if (!$status) return 'no_token';
                        return $status['active'] ? 'active' : 'inactive';
                    })
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'active' => __('admin.active'),
                            'inactive' => __('admin.inactive'),
                            'no_token' => __('admin.no_token'),
                            default => $state,
                        };
                    })
                    ->colors([
                        'success' => 'active',
                        'danger' => 'inactive',
                        'gray' => 'no_token',
                    ]),

                Tables\Columns\BadgeColumn::make('token_health')
                    ->label(__('admin.token_status'))
                    ->getStateUsing(function ($record, $rowLoop): string {
                        $data = $this->healthData->get($rowLoop->index);
                        $status = $data['status'] ?? null;
                        if (!$status || !$status['exists']) return 'missing';
                        if (!$status['active']) return 'inactive';
                        if ($status['access_token_expired']) return 'expired';
                        if ($status['needs_refresh']) return 'needs_refresh';
                        return 'healthy';
                    })
                    ->formatStateUsing(function (string $state): string {
                        return match ($state) {
                            'healthy' => __('admin.healthy'),
                            'needs_refresh' => __('admin.needs_refresh'),
                            'expired' => __('admin.expired'),
                            'inactive' => __('admin.inactive'),
                            'missing' => __('admin.missing'),
                            default => $state,
                        };
                    })
                    ->colors([
                        'success' => 'healthy',
                        'warning' => 'needs_refresh',
                        'danger' => ['expired', 'missing'],
                        'gray' => 'inactive',
                    ]),

                Tables\Columns\TextColumn::make('expires_at')
                    ->label(__('admin.access_expires'))
                    ->getStateUsing(function ($record, $rowLoop): string {
                        $data = $this->healthData->get($rowLoop->index);
                        $status = $data['status'] ?? null;
                        if (!$status || !$status['expires_at']) return 'هرگز';
                        return \Carbon\Carbon::parse($status['expires_at'])->format('Y-m-d H:i') . 
                               ' (' . \Carbon\Carbon::parse($status['expires_at'])->diffForHumans() . ')';
                    }),

                Tables\Columns\TextColumn::make('last_used')
                    ->label(__('admin.last_used'))
                    ->getStateUsing(function ($record, $rowLoop): string {
                        $data = $this->healthData->get($rowLoop->index);
                        $status = $data['status'] ?? null;
                        if (!$status || !$status['last_used_at']) return 'هرگز';
                        return \Carbon\Carbon::parse($status['last_used_at'])->diffForHumans();
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('refresh')
                    ->label(__('admin.refresh_token'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->action(function ($record, $rowLoop) use ($tokenService) {
                        $data = collect([
                            [
                                'provider' => 'جیبیت',
                                'provider_key' => Token::PROVIDER_JIBIT,
                            ],
                            [
                                'provider' => 'فین‌تک', 
                                'provider_key' => Token::PROVIDER_FINNOTECH,
                            ]
                        ])->get($rowLoop->index);

                        if ($data) {
                            $success = $tokenService->refreshToken($data['provider_key']);
                            
                            if ($success) {
                                \Filament\Notifications\Notification::make()
                                    ->title('توکن تازه‌سازی شد')
                                    ->body('توکن ' . $data['provider'] . ' با موفقیت تازه‌سازی شد')
                                    ->success()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('تازه‌سازی ناموفق بود')
                                    ->body('تازه‌سازی توکن ' . $data['provider'] . ' ناموفق بود')
                                    ->danger()
                                    ->send();
                            }
                        }
                    }),

                Tables\Actions\Action::make('test')
                    ->label(__('admin.test_token'))
                    ->icon('heroicon-o-wrench-screwdriver')
                    ->color('info')
                    ->action(function ($record, $rowLoop) use ($tokenService) {
                        $data = collect([
                            [
                                'provider' => 'جیبیت',
                                'provider_key' => Token::PROVIDER_JIBIT,
                            ],
                            [
                                'provider' => 'فین‌تک',
                                'provider_key' => Token::PROVIDER_FINNOTECH,
                            ]
                        ])->get($rowLoop->index);

                        if ($data) {
                            $isValid = $tokenService->ensureValidToken($data['provider_key']);
                            
                            if ($isValid) {
                                \Filament\Notifications\Notification::make()
                                    ->title('توکن معتبر است')
                                    ->body('توکن ' . $data['provider'] . ' به درستی کار می‌کند')
                                    ->success()
                                    ->send();
                            } else {
                                \Filament\Notifications\Notification::make()
                                    ->title('توکن نامعتبر است')
                                    ->body('توکن ' . $data['provider'] . ' کار نمی‌کند')
                                    ->warning()
                                    ->send();
                            }
                        }
                    }),
            ])
            ->emptyStateHeading('داده‌ای برای سلامت توکن وجود ندارد')
            ->emptyStateDescription('امکان دریافت اطلاعات سلامت توکن وجود ندارد.')
            ->paginated(false);
    }
} 