<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Services\AiService;
use Filament\Notifications\Notification;

class EditService extends EditRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('generateSeoMeta')
                ->label(__('filament-panels::resources/service.actions.generate_seo_meta'))
                ->icon('heroicon-o-sparkles')
                ->action(function (AiService $aiService) {
                    $meta = $aiService->generateMeta($this->record);
                    $this->record->update([
                        'meta_title' => $meta['title'],
                        'meta_description' => $meta['description'],
                        'meta_keywords' => $meta['keywords'],
                        'og_title' => $meta['og_title'],
                        'og_description' => $meta['og_description'],
                        'twitter_title' => $meta['twitter_title'],
                        'twitter_description' => $meta['twitter_description'],
                    ]);
                    Notification::make()
                        ->success()
                        ->title(__('filament-panels::resources/service.notifications.seo_meta_generated.title'))
                        ->body(__('filament-panels::resources/service.notifications.seo_meta_generated.body'));
                }),
            Actions\Action::make('generateSchema')
                ->label(__('filament-panels::resources/service.actions.generate_schema'))
                ->icon('heroicon-o-code-bracket')
                ->action(function (AiService $aiService) {
                    $schema = $aiService->generateSchema($this->record);
                    dd($schema);
                    $this->record->update([
                        'schema' => $schema['schema'],
                        'json_ld' => $schema['json_ld'],
                    ]);
                    Notification::make()
                        ->success()
                        ->title(__('filament-panels::resources/service.notifications.schema_generated.title'))
                        ->body(__('filament-panels::resources/service.notifications.schema_generated.body'));
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ServiceResource\Widgets\ServiceStats::class,
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('filament-panels::resources/service.notifications.updated.title'))
            ->body(__('filament-panels::resources/service.notifications.updated.body'));
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Determine content type based on current content value
        if (isset($data['content']) && is_numeric($data['content'])) {
            $data['content_type'] = 'ai';
        } else {
            $data['content_type'] = 'manual';
        }
        
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Remove content_type as it's not a database field
        unset($data['content_type']);
        
        return $data;
    }

    protected function afterSave(): void
    {
        // Perform any additional actions after saving the Service
        // For example, you could trigger a cache clear or send notifications
    }
}