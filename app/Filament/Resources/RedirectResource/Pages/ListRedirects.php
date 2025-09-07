<?php

namespace App\Filament\Resources\RedirectResource\Pages;

use App\Filament\Resources\RedirectResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListRedirects extends ListRecords
{
    protected static string $resource = RedirectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('تغییر مسیر جدید'),
                
            Actions\Action::make('cache_stats')
                ->label('آمار کش')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->action(function () {
                    $stats = \App\Models\Redirect::getCacheStats();
                    $total = \App\Models\Redirect::where('is_active', true)->count();
                    
                    \Filament\Notifications\Notification::make()
                        ->title('آمار کش ریدایرکت‌ها')
                        ->body("کش فعال: " . ($stats['active_redirects_cached'] ? 'بله' : 'خیر') . 
                               " | کلیدهای کش: {$stats['cache_keys_count']}" .
                               " | ریدایرکت‌های فعال: {$total}")
                        ->info()
                        ->persistent()
                        ->send();
                }),

            Actions\Action::make('warm_cache')
                ->label('گرم کردن کش')
                ->icon('heroicon-o-fire')
                ->color('warning')
                ->action(function () {
                    \App\Models\Redirect::getAllActiveRedirects();
                    \App\Models\Redirect::preloadPopularRedirects();
                    
                    \Filament\Notifications\Notification::make()
                        ->title('کش گرم شد')
                        ->body('کش تمام ریدایرکت‌ها و محبوب‌ترین آدرس‌ها گرم شد')
                        ->success()
                        ->send();
                }),

            Actions\Action::make('clear_all_cache')
                ->label('پاک کردن کل کش')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->action(function () {
                    \App\Models\Redirect::clearAllCache();
                    
                    \Filament\Notifications\Notification::make()
                        ->title('کش پاک شد')
                        ->body('تمام کش ریدایرکت‌ها پاک شد')
                        ->success()
                        ->send();
                })
                ->requiresConfirmation()
                ->modalHeading('پاک کردن کل کش')
                ->modalDescription('آیا مطمئن هستید که می‌خواهید تمام کش ریدایرکت‌ها را پاک کنید؟')
                ->modalSubmitActionLabel('بله، پاک کن'),
        ];
    }
}