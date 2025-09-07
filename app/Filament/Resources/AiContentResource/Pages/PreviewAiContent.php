<?php

namespace App\Filament\Resources\AiContentResource\Pages;

use App\Filament\Resources\AiContentResource;
use Filament\Resources\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;

class PreviewAiContent extends Page
{
    protected static string $resource = AiContentResource::class;
    protected static string $view = 'filament.resources.ai-content-resource.pages.preview-ai-content';
    public $record;
    public $previewHtml;
    public $showH1 = false;
    public $wrapper = 'article';

    public function mount($record): void
    {
        $this->record = $this->resolveRecord($record);
        $this->refreshPreview();
    }

    public function refreshPreview(): void
    {
        $this->previewHtml = $this->record->generateUnifiedHtml(
            $this->showH1,
            $this->wrapper
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('copyHtml')
                ->label('Copy HTML')
                ->color('success')
                ->action(function () {
                    $this->dispatch('copyToClipboard', html: $this->previewHtml);
                    Notification::make()
                        ->title('HTML copied to clipboard')
                        ->success()
                        ->send();
                }),
            Action::make('back')
                ->label('Back to Edit')
                ->url(fn () => AiContentResource::getUrl('edit', ['record' => $this->record]))
                ->color('gray'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function resolveRecord($record): mixed
    {
        return static::$resource::resolveRecordRouteBinding($record);
    }
}