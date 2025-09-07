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
            ->label('PERSIAN_TEXT_e3857236')
            ->icon('heroicon-o-sparkles')
            ->color('warning')
            ->requiresConfirmation()
            ->modalHeading('PERSIAN_TEXT_1a130efa')
            ->modalDescription('PERSIAN_TEXT_db8ff978')
            ->modalSubmitActionLabel('PERSIAN_TEXT_3cfdcdb4')
            ->form([
                Forms\Components\Toggle::make('use_queue')
                    ->label('PERSIAN_TEXT_60e3990d')
                    ->helperText('PERSIAN_TEXT_be801b37')
                    ->default(true),
                Forms\Components\Toggle::make('parallel_generation')
                    ->label('PERSIAN_TEXT_e037ecce')
                    ->helperText('PERSIAN_TEXT_561ce49c')
                    ->default(false),
                Forms\Components\Toggle::make('use_web_search')
                    ->label('PERSIAN_TEXT_775698cb')
                    ->default(true),
                Forms\Components\Toggle::make('generate_images')
                    ->label('PERSIAN_TEXT_00b4f311')
                    ->default(false),
                Forms\Components\Select::make('priority')
                    ->label('PERSIAN_TEXT_44e7afcf')
                    ->options([
                        'high' => 'PERSIAN_TEXT_6986ecfe',
                        'normal' => 'PERSIAN_TEXT_49415c45',
                        'low' => 'PERSIAN_TEXT_a674038d',
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
                    ->title('PERSIAN_TEXT_75185aa6')
                    ->body("PERSIAN_TEXT_c7ef1edb")
                    ->send();
            })
            ->deselectRecordsAfterCompletion();
    }
}