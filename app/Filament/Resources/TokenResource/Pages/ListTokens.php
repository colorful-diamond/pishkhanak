<?php

namespace App\Filament\Resources\TokenResource\Pages;

use App\Filament\Resources\TokenResource;
use App\Services\TokenService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class ListTokens extends ListRecords
{
    protected static string $resource = TokenResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            
            Actions\Action::make('sync_tokens')
                ->label(__('admin.auto_generate_missing_tokens'))
                ->icon('heroicon-o-plus-circle')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading(__('admin.auto_generate_missing_tokens'))
                ->modalDescription(__('admin.automatically_generate_tokens'))
                ->action(function () {
                    try {
                        $tokenService = app(TokenService::class);
                        $results = [];
                        
                        // Check and generate Jibit token if missing
                        if (!$tokenService->getAccessToken('jibit')) {
                            $results['jibit'] = $tokenService->generateJibitToken();
                        }
                        
                        // Check and generate Finnotech token if missing
                        if (!$tokenService->getAccessToken('finnotech')) {
                            $results['finnotech'] = $tokenService->generateFinnotechToken();
                        }
                        
                        if (empty($results)) {
                            Notification::make()
                                ->title(__('admin.no_missing_tokens'))
                                ->body('همه ارائه‌دهندگان از قبل دارای توکن فعال هستند.')
                                ->info()
                                ->send();
                        } else {
                            $successCount = array_sum($results);
                            $totalCount = count($results);
                            
                            Notification::make()
                                ->title(__('admin.auto_generation_completed'))
                                ->body("{$successCount} از {$totalCount} توکن گمشده تولید شد.")
                                ->success()
                                ->send();
                        }
                    } catch (\Exception $e) {
                        Log::error('Auto token generation failed: ' . $e->getMessage());
                        
                        Notification::make()
                            ->title('تولید خودکار ناموفق بود')
                            ->body('خطایی رخ داده است: ' . $e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\TokenResource\Widgets\TokenStatsWidget::class,
            \App\Filament\Resources\TokenResource\Widgets\TokenHealthWidget::class,
        ];
    }
} 