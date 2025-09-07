<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Services\AiService;
use Filament\Notifications\Notification;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
            Actions\Action::make('generateSeoMeta')
                ->label(__('filament-panels::resources/post.actions.generate_seo_meta'))
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
                        ->title(__('filament-panels::resources/post.notifications.seo_meta_generated.title'))
                        ->body(__('filament-panels::resources/post.notifications.seo_meta_generated.body'));
                }),
            Actions\Action::make('generateSchema')
                ->label(__('filament-panels::resources/post.actions.generate_schema'))
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
                        ->title(__('filament-panels::resources/post.notifications.schema_generated.title'))
                        ->body(__('filament-panels::resources/post.notifications.schema_generated.body'));
                }),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            PostResource\Widgets\PostStats::class,
        ];
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('filament-panels::resources/post.notifications.updated.title'))
            ->body(__('filament-panels::resources/post.notifications.updated.body'));
    }

    protected function afterSave(): void
    {
        // Perform any additional actions after saving the post
        // For example, you could trigger a cache clear or send notifications
    }


}
