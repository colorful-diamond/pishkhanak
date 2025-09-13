<?php

namespace App\Filament\Resources\BlogPipelineResource\Actions;

use App\Jobs\ProcessBlogContentJob;
use App\Models\BlogContentPipeline;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;

class BulkGenerateContentAction extends BulkAction
{
    public static function make(?string $name = null): static
    {
        return parent::make($name ?? 'bulk_generate')
            ->label('تولید گروهی محتوا')
            ->icon('heroicon-o-sparkles')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('تولید محتوا برای مقالات انتخابی')
            ->modalDescription('آیا مطمئن هستید که می‌خواهید برای مقالات انتخابی محتوا تولید کنید؟')
            ->modalSubmitActionLabel('شروع تولید')
            ->form([
                Forms\Components\Toggle::make('use_queue')
                    ->label('استفاده از صف پردازش')
                    ->helperText('تولید محتوا در پس‌زمینه انجام شود')
                    ->default(true),
                Forms\Components\Toggle::make('parallel_generation')
                    ->label('تولید موازی')
                    ->helperText('چندین مقاله به صورت هم‌زمان تولید شود')
                    ->default(false),
                Forms\Components\Toggle::make('use_web_search')
                    ->label('استفاده از جستجوی وب')
                    ->default(true),
                Forms\Components\Toggle::make('generate_images')
                    ->label('تولید تصاویر')
                    ->default(false),
                Forms\Components\Select::make('priority')
                    ->label('اولویت')
                    ->options([
                        'high' => 'بالا',
                        'normal' => 'عادی',
                        'low' => 'پایین',
                    ])
                    ->default('normal'),
            ])
            ->action(function (Collection $records, array $data): void {
                $count = 0;
                
                foreach ($records as $record) {
                    // Skip already processed records
                    if (in_array($record->status, [
                        BlogContentPipeline::STATUS_PROCESSING,
                        BlogContentPipeline::STATUS_PROCESSED,
                        BlogContentPipeline::STATUS_PUBLISHED
                    ])) {
                        continue;
                    }
                    
                    if ($data['use_queue'] ?? true) {
                        // Queue the job
                        ProcessBlogContentJob::dispatch($record->id)
                            ->onQueue($data['priority'] ?? 'normal');
                        
                        $record->update([
                            'status' => BlogContentPipeline::STATUS_QUEUED,
                        ]);
                    } else {
                        // Process immediately (not recommended for many posts)
                        $record->update([
                            'status' => BlogContentPipeline::STATUS_PROCESSING,
                        ]);
                        
                        ProcessBlogContentJob::dispatchSync($record->id);
                    }
                    
                    $count++;
                }
                
                Notification::make()
                    ->success()
                    ->title('تولید محتوا آغاز شد')
                    ->body("تولید محتوا برای {$count} مقاله در صف قرار گرفت و به زودی تکمیل خواهد شد.")
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}