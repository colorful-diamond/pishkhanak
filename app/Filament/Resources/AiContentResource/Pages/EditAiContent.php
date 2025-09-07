<?php
namespace App\Filament\Resources\AiContentResource\Pages;

use App\Filament\Resources\AiContentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\ViewField;
use Filament\Notifications\Notification;
use App\Services\AiService;
use App\Jobs\GenerateSectionContentJob;
use Illuminate\Support\Facades\Auth;

class EditAiContent extends EditRecord
{
    protected static string $resource = AiContentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view_source')
                ->label('View Source')
                ->icon('heroicon-o-link')
                ->visible(fn () => $this->record->model_type && $this->record->model_id)
                ->action(function () {
                    // Navigate to source model
                    $modelType = $this->record->model_type;
                    if ($modelType === 'Service') {
                        return redirect()->route('filament.admin.resources.services.edit', ['record' => $this->record->model_id]);
                    }
                }),
            
            ActionGroup::make([
                Action::make('regenerate_all')
                    ->label('Regenerate All Content')
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->action(function () {
                        $this->regenerateAllContent();
                    }),
                    
                Action::make('regenerate_failed')
                    ->label('Regenerate Failed Sections')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->visible(fn () => $this->hasFailedSections())
                    ->requiresConfirmation()
                    ->action(function () {
                        $this->regenerateFailedSections();
                    }),
                    
                Action::make('generate_missing')
                    ->label('Generate Missing Sections')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->visible(fn () => $this->hasMissingSections())
                    ->action(function () {
                        $this->generateMissingSections();
                    }),
            ])
            ->label('AI Actions')
            ->icon('heroicon-o-sparkles'),
            
            DeleteAction::make(),
        ];
    }
    
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Add generation status data for display
        if ($this->record) {
            $data['_generation_status'] = $this->getGenerationStatusSummary();
        }
        
        return $data;
    }
    
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Update last edited by
        $data['last_edited_by'] = Auth::id();
        
        return $data;
    }
    
    protected function afterSave(): void
    {
        // Clear any cached content
        cache()->forget('ai_content_' . $this->record->id);
        
        Notification::make()
            ->title('Content saved successfully')
            ->success()
            ->send();
    }
    
    protected function getFormActions(): array
    {
        return array_merge(
            parent::getFormActions(),
            [
                Action::make('save_and_preview')
                    ->label('Save & Preview')
                    ->action('saveAndPreview')
                    ->color('success'),
            ]
        );
    }
    
    public function saveAndPreview(): void
    {
        $this->save();
        $this->redirect(AiContentResource::getUrl('preview', ['record' => $this->record]));
    }
    
    /**
     * Helper methods for AI regeneration
     */
    protected function hasFailedSections(): bool
    {
        $statuses = $this->record->section_generation_status ?? [];
        return in_array('failed', $statuses);
    }
    
    protected function hasMissingSections(): bool
    {
        if (empty($this->record->ai_headings)) {
            return false;
        }
        
        $headings = collect($this->record->ai_headings);
        $sections = collect($this->record->ai_sections ?? []);
        
        return $headings->count() > $sections->count();
    }
    
    protected function getGenerationStatusSummary(): array
    {
        $statuses = $this->record->section_generation_status ?? [];
        
        return [
            'total' => count($this->record->ai_headings ?? []),
            'completed' => count(array_filter($statuses, fn($s) => $s === 'completed')),
            'failed' => count(array_filter($statuses, fn($s) => $s === 'failed')),
            'generating' => count(array_filter($statuses, fn($s) => $s === 'generating')),
        ];
    }
    
    protected function regenerateAllContent(): void
    {
        $this->record->update([
            'status' => 'generating',
            'generation_progress' => 0,
            'current_generation_step' => 'regenerating_all',
            'section_generation_status' => [],
            'generation_started_at' => now(),
        ]);
        
        // Dispatch regeneration jobs for all sections
        $this->dispatchSectionJobs();
        
        Notification::make()
            ->title('Content regeneration started')
            ->body('All sections will be regenerated in the background.')
            ->success()
            ->send();
    }
    
    protected function regenerateFailedSections(): void
    {
        $statuses = $this->record->section_generation_status ?? [];
        $failedSections = array_keys(array_filter($statuses, fn($s) => $s === 'failed'));
        
        foreach ($failedSections as $sectionHeading) {
            $this->record->updateSectionStatus($sectionHeading, 'generating');
        }
        
        $this->record->update([
            'status' => 'generating',
            'current_generation_step' => 'regenerating_failed',
        ]);
        
        // Dispatch jobs only for failed sections
        $this->dispatchSectionJobs($failedSections);
        
        Notification::make()
            ->title('Failed sections regeneration started')
            ->body(count($failedSections) . ' failed sections will be regenerated.')
            ->success()
            ->send();
    }
    
    protected function generateMissingSections(): void
    {
        $headings = collect($this->record->ai_headings);
        $existingSections = collect($this->record->ai_sections ?? [])->pluck('heading');
        $missingSections = $headings->pluck('title')->diff($existingSections);
        
        $this->record->update([
            'status' => 'generating',
            'current_generation_step' => 'generating_missing',
        ]);
        
        // Dispatch jobs for missing sections
        $this->dispatchSectionJobs($missingSections->toArray());
        
        Notification::make()
            ->title('Missing sections generation started')
            ->body(count($missingSections) . ' missing sections will be generated.')
            ->success()
            ->send();
    }
    
    protected function dispatchSectionJobs(array $specificSections = []): void
    {
        $headings = collect($this->record->ai_headings);
        $sectionsToGenerate = empty($specificSections) 
            ? $headings 
            : $headings->filter(fn($h) => in_array($h['title'], $specificSections));
        
        $totalSections = $sectionsToGenerate->count();
        $number = 1;
        
        foreach ($sectionsToGenerate as $heading) {
            GenerateSectionContentJob::dispatch(
                $this->record->id,
                $heading,
                $this->record->title,
                $this->record->short_description,
                $this->record->language,
                $this->record->model_type,
                $number,
                $totalSections,
                $this->record->generation_settings['generation_mode'] ?? 'offline'
            )->onQueue('ai-content');
            
            $number++;
        }
    }
    
    /**
     * Regenerate a single section via AJAX
     */
    public function regenerateSection(int $sectionIndex, string $heading): array
    {
        try {
            // Mark section as generating
            $this->record->updateSectionStatus($heading, 'generating');
            
            // Get the AI service
            $aiService = app(AiService::class);
            
            // Generate new content for this section
            $result = $aiService->generateSectionContent(
                $heading,
                $this->record->title,
                $this->record->short_description,
                1, // section number
                1, // total sections (just this one)
                $this->record->language,
                $this->record->generation_settings['model_type'] ?? 'fast',
                $this->record->generation_settings['generation_mode'] ?? 'offline'
            );
            
            if ($result && isset($result['content'])) {
                // Update the section in the database
                $sections = $this->record->ai_sections ?? [];
                $sections[$sectionIndex] = [
                    'heading' => $heading,
                    'content' => $result['content']
                ];
                
                $this->record->update([
                    'ai_sections' => $sections,
                    'last_edited_by' => Auth::id()
                ]);
                
                // Mark section as completed
                $this->record->updateSectionStatus($heading, 'completed');
                
                return [
                    'success' => true,
                    'section' => [
                        'heading' => $heading,
                        'content' => $result['content']
                    ]
                ];
            } else {
                throw new \Exception('Failed to generate content');
            }
        } catch (\Exception $e) {
            // Mark section as failed
            $this->record->updateSectionStatus($heading, 'failed');
            
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}