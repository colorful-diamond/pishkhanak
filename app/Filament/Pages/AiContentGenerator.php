<?php

namespace App\Filament\Pages;

use App\Models\AiContent;
use App\Models\AiSetting;
use App\Services\AiService;
use App\Filament\Resources\AiSettingResource;
use App\Filament\Resources\AiContentResource;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Support\Exceptions\Halt;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Queue;
use App\Jobs\GenerateSectionContentJob;
use App\Events\CustomUserEvent;

class AiContentGenerator extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static string $view = 'filament.pages.ai-content-generator';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 1;
    protected static ?string $title = 'AI Content Generator';

    public static function getNavigationLabel(): string
    {
        return __('ai_content.ai_content_generator');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('ai_content.navigation_group');
    }

    public function getTitle(): string
    {
        return __('ai_content.ai_content_generator');
    }

    public ?array $data = [];
    protected $aiSetting;

    public function mount(): void
    {
        // Get the active AI setting
        $this->aiSetting = AiSetting::where('is_active', true)->first();
        if (!$this->aiSetting) {
            Notification::make()
                ->warning()
                ->title(__('ai_content.no_active_ai_settings'))
                ->body(__('ai_content.configure_ai_settings_first'))
                ->persistent()
                ->send();

            $this->redirect(AiSettingResource::getUrl());
            return;
        }

        // Initialize form with default values from AI settings
        $this->form->fill([
            'language' => $this->aiSetting->language_settings['default_language'] ?? 'en',
            'model_type' => array_key_first($this->aiSetting->model_config),
            'generation_mode' => 'offline',
            'headings_number' => $this->aiSetting->generation_settings['headings_number'] ?? 8,
            'sub_headings_number' => $this->aiSetting->generation_settings['sub_headings_number'] ?? 3,
            'tone' => array_key_first($this->aiSetting->tone_settings),
            'contentFormat' => array_key_first($this->aiSetting->content_formats),
            'targetAudience' => array_key_first($this->aiSetting->target_audiences),
            'includeImages' => false,
            'enableAutoSave' => $this->aiSetting->generation_settings['enable_auto_save'] ?? true,
        ]);
    }

    public function form(Form $form): Form
    {
        if (!$this->aiSetting) {
            return $form->schema([
                Section::make(__('ai_content.configuration_error'))
                    ->schema([
                        \Filament\Forms\Components\Placeholder::make('error')
                            ->label(__('ai_content.configuration_error'))
                            ->content(__('ai_content.no_active_ai_settings') . '. ' . __('ai_content.configure_ai_settings_first'))
                    ])
            ]);
        }

        return $form
            ->schema([
                Grid::make(3)
                    ->schema([
                        Section::make(__('ai_content.basic_information'))
                            ->schema([
                                Select::make('model_name')
                                    ->label(__('ai_content.content_type'))
                                    ->options(collect($this->aiSetting?->model_config ?? [])->pluck('label', 'name'))
                                    ->reactive()
                                    ->afterStateUpdated(fn (Set $set) => $set('search_title', '')),

                                TextInput::make('search_title')
                                    ->label(__('ai_content.search_existing_content'))
                                    ->placeholder(__('ai_content.start_typing_to_search'))
                                    ->hidden(fn (Get $get) => !$get('model_name'))
                                    ->reactive()
                                    ->debounce(300)
                                    ->afterStateUpdated(function ($state, Set $set, Get $get) {
                                        if (strlen($state) >= 2) {
                                            $modelConfig = collect($this->aiSetting->model_config)
                                                ->firstWhere('name', $get('model_name'));
                                            if ($modelConfig) {
                                                $results = $this->searchContent($state, $modelConfig);
                                                $set('searchResults', $results);
                                            }
                                        } else {
                                            $set('searchResults', []);
                                        }
                                    }),

                                TextInput::make('title')
                                    ->label(__('ai_content.title'))
                                    ->required()
                                    ->maxLength(255),

                                Textarea::make('short_description')
                                    ->label(__('ai_content.short_description'))
                                    ->required()
                                    ->rows(3),
                            ])
                            ->columnSpan(2),

                        Section::make(__('ai_content.generation_settings'))
                            ->schema([
                                Select::make('language')
                                    ->label(__('ai_content.language'))
                                    ->options($this->aiSetting?->language_settings['supported_languages'] ?? [])
                                    ->required(),

                                Select::make('model_type')
                                    ->label(__('ai_content.model_type'))
                                    ->options(collect($this->aiSetting?->model_config ?? [])->pluck('model_type', 'name'))
                                    ->required(),

                                Select::make('tone')
                                    ->label(__('ai_content.tone'))
                                    ->options(collect($this->aiSetting?->tone_settings ?? [])->pluck('label', 'name'))
                                    ->required()
                                    ->helperText(fn (Get $get) => collect($this->aiSetting?->tone_settings ?? [])->firstWhere('name', $get('tone'))['description'] ?? ''),

                                Select::make('contentFormat')
                                    ->label(__('ai_content.content_format'))
                                    ->options(collect($this->aiSetting?->content_formats ?? [])->pluck('label', 'name'))
                                    ->required()
                                    ->helperText(fn (Get $get) => collect($this->aiSetting?->content_formats ?? [])->firstWhere('name', $get('contentFormat'))['description'] ?? ''),

                                Select::make('targetAudience')
                                    ->label(__('ai_content.target_audience'))
                                    ->options(collect($this->aiSetting?->target_audiences ?? [])->pluck('label', 'name'))
                                    ->required()
                                    ->helperText(fn (Get $get) => collect($this->aiSetting?->target_audiences ?? [])->firstWhere('name', $get('targetAudience'))['description'] ?? ''),

                                TextInput::make('headings_number')
                                    ->label(__('ai_content.headings_number'))
                                    ->numeric()
                                    ->minValue($this->aiSetting?->generation_settings['min_headings'] ?? 1)
                                    ->maxValue($this->aiSetting?->generation_settings['max_headings'] ?? 10)
                                    ->required(),

                                TextInput::make('sub_headings_number')
                                    ->label(__('ai_content.sub_headings_number'))
                                    ->numeric()
                                    ->minValue($this->aiSetting?->generation_settings['min_sub_headings'] ?? 1)
                                    ->maxValue($this->aiSetting?->generation_settings['max_sub_headings'] ?? 5)
                                    ->required(),

                                Toggle::make('includeImages')
                                    ->label(__('ai_content.include_ai_image_suggestions'))
                                    ->visible(fn () => $this->aiSetting?->generation_settings['enable_images'] ?? false),

                                Toggle::make('enableAutoSave')
                                    ->label(__('ai_content.enable_auto_save'))
                                    ->visible(fn () => $this->aiSetting?->generation_settings['enable_auto_save'] ?? true),

                                Toggle::make('online_mode')
                                    ->label(__('ai_content.enable_online_research'))
                                    ->visible(fn () => $this->aiSetting?->generation_settings['enable_online_mode'] ?? false)
                                    ->helperText(__('ai_content.allows_ai_search_web')),
                            ])
                            ->columnSpan(1),
                    ]),
            ])
            ->statePath('data');
    }

    protected function searchContent($query, $modelConfig)
    {
        try {
            $modelClass = $modelConfig['model'];
            $searchFields = $modelConfig['searchable_fields'] ?? ['title', 'name', 'slug'];

            $queryBuilder = $modelClass::query();

            // Build search query
            $queryBuilder->where(function ($q) use ($query, $searchFields) {
                foreach ($searchFields as $index => $field) {
                    if ($index === 0) {
                        $q->where($field, 'like', "%{$query}%");
                    } else {
                        $q->orWhere($field, 'like', "%{$query}%");
                    }
                }
            });

            return $queryBuilder->limit(10)->get();
        } catch (\Exception $e) {
            Log::error('Content search failed', [
                'error' => $e->getMessage(),
                'model_config' => $modelConfig,
            ]);
            return [];
        }
    }

    public function generate(): void
    {
        try {
            $this->validate();

            $eventHash = Str::random(7);

            // Create AI Content record
            $aiContent = AiContent::create([
                'title' => $this->data['title'],
                'slug' => Str::slug($this->data['title']) . '-' . Str::random(6),
                'short_description' => $this->data['short_description'],
                'language' => $this->data['language'],
                'model_type' => $this->data['model_type'],
                'tone' => $this->data['tone'],
                'content_format' => $this->data['contentFormat'],
                'target_audience' => $this->data['targetAudience'],
                'status' => 'generating',
                'author_id' => Auth::id(),
                'settings' => [
                    'headings_number' => $this->data['headings_number'],
                    'sub_headings_number' => $this->data['sub_headings_number'],
                    'include_images' => $this->data['includeImages'] ?? false,
                    'online_mode' => $this->data['online_mode'] ?? false,
                    'auto_save' => $this->data['enableAutoSave'] ?? true,
                ],
            ]);

            // Initialize the generation process
            $this->startGeneration($aiContent, $eventHash);

            Notification::make()
                ->success()
                ->title(__('ai_content.generation_started'))
                ->body(__('ai_content.content_generation_initiated'))
                ->send();

            $this->redirect(AiContentResource::getUrl('edit', ['record' => $aiContent]));

        } catch (Halt $exception) {
            Notification::make()
                ->danger()
                ->title(__('ai_content.validation_error'))
                ->body(__('ai_content.check_form_errors'))
                ->send();
        } catch (\Exception $e) {
            Log::error('Content generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            Notification::make()
                ->danger()
                ->title(__('ai_content.generation_failed'))
                ->body(__('ai_content.error_generating_content'))
                ->send();
        }
    }

    protected function startGeneration($aiContent, $eventHash): void
    {
        $aiService = app(AiService::class);

        // Generate headings
        $headingsJson = $aiService->cleanJson($aiService->generateTitles(
            $this->data['title'],
            $this->data['short_description'],
            $this->data['language'],
            $this->data['model_type'],
            $this->data['headings_number'],
            $this->data['sub_headings_number'],
            $this->data['online_mode'] ?? false
        ));

        $headlines = json_decode($headingsJson, true);
        if (is_array($headlines)) {
            $aiContent->ai_headings = count($headlines) == 1 ? $headlines[array_key_first($headlines)] : $headlines;
            $aiContent->save();

            // Create jobs for each heading
            foreach ($aiContent->ai_headings as $index => $heading) {
                Queue::push(new GenerateSectionContentJob(
                    $heading,
                    $aiContent->title,
                    $aiContent->short_description,
                    $aiContent->language,
                    $aiContent->model_type,
                    $index + 1,
                    count($aiContent->ai_headings),
                    $aiContent->id,
                    AiContent::class
                ));
            }

            // Dispatch event for monitoring
            event(new CustomUserEvent(
                $eventHash,
                'monitorSectionGeneration',
                null,
                'function'
            ));
        }
    }

    protected function getFormActions(): array
    {
        return [
            FormAction::make('generate')
                ->label(__('ai_content.generate_content'))
                ->color('primary')
                ->icon('heroicon-o-sparkles')
                ->submit('generate'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('settings')
                ->label(__('ai_content.ai_settings'))
                ->icon('heroicon-o-cog')
                ->url(AiSettingResource::getUrl())
                ->color('gray'),
        ];
    }
}
