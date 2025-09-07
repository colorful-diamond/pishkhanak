<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Services\AiService;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Forms;
class CreatePost extends CreateRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('generateContent')
                ->label(__('filament-panels::resources/post.fields.generate_ai_content'))
                ->icon('heroicon-o-sparkles')
                ->form([
                    Forms\Components\TextInput::make('title')
                        ->label(__('filament-panels::resources/post.fields.title'))
                        ->required()
                        ->maxLength(255),

                    Forms\Components\Textarea::make('short_description')
                        ->label(__('filament-panels::resources/post.fields.ai_short_description'))
                        ->required()
                        ->maxLength(600),

                    Forms\Components\Select::make('model_type')
                        ->label(__('filament-panels::resources/post.fields.model_type'))
                        ->options([
                            'advanced' => 'Advanced',
                            'fast' => 'Fast',
                        ])
                        ->default('fast'),

                    Forms\Components\Select::make('language')
                        ->label(__('filament-panels::resources/post.fields.language'))
                        ->options([
                            'Persian' => 'فارسی',
                            'English' => 'English',
                            'Spanish' => 'Spanish',
                            'Hindi' => 'Hindi',
                            'Arabic' => 'Arabic',
                            'Bengali' => 'Bengali',
                            'Portuguese' => 'Portuguese',
                            'Russian' => 'Russian',
                            'Japanese' => 'Japanese',
                            'German' => 'German',
                            'French' => 'French',
                            'Turkish' => 'Turkish',
                        ])
                        ->default('Persian')
                        ->required(),
                ])
                ->action(function (array $data, AiService $aiService) {
                    $url = $aiService->generateContent($data['title'] ?? '', $data['short_description'] ?? '', $data['language'] ?? 'Persian', $data['model_type'] ?? 'fast');
                    if($url){
                        return redirect($url);
                    }
                    Notification::make()
                        ->title('Error')
                        ->body(__('filament-panels::resources/post.fields.generate_ai_content_error'))
                        ->danger()
                        ->send();
                })
                ->requiresConfirmation(),
        ];
    }

    protected function afterCreate(): void
    {
        // Generate SEO meta and schema after post creation
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

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('edit', ['record' => $this->record]);
    }
}