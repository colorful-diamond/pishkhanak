<?php

namespace App\Filament\Resources\ServiceResource\Pages;

use App\Filament\Resources\ServiceResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Services\AiService;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms;
class CreateService extends CreateRecord
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generateContent')
                ->label(__('filament-panels::resources/service.fields.generate_ai_content'))
                ->icon('heroicon-o-sparkles')
                ->form([
                    Forms\Components\TextInput::make('title')
                        ->label(__('filament-panels::resources/service.fields.title'))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Textarea::make('short_description')
                        ->label(__('filament-panels::resources/service.fields.ai_short_description'))
                        ->required()
                        ->maxLength(255),
                ])
                ->action(function (array $data, AiService $aiService) {
                    $url = $aiService->generateContent($data['title'] ?? '', $data['short_description'] ?? '');
                    if($url){
                        return redirect($url);
                    }
                    Notification::make()
                        ->title('Error')
                        ->body(__('filament-panels::resources/service.fields.generate_ai_content_error'))
                        ->danger()
                        ->send();
                })
                ->requiresConfirmation(),
        ];
    }

    protected function afterCreate(): void
    {
        // Generate SEO meta and schema after Service creation
        // $aiService = app(AiService::class);
        // $meta = $aiService->generateMeta($this->record);
        // $schema = $aiService->generateSchema($this->record);

        // $this->record->update([
        //     'meta_title' => $meta['title'],
        //     'meta_description' => $meta['description'],
        //     'meta_keywords' => $meta['keywords'],
        //     'og_title' => $meta['og_title'],
        //     'og_description' => $meta['og_description'],
        //     'twitter_title' => $meta['twitter_title'],
        //     'twitter_description' => $meta['twitter_description'],
        //     'schema' => $schema['schema'],
        //     'json_ld' => $schema['json_ld'],
        // ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Remove content_type as it's not a database field
        unset($data['content_type']);
        
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}