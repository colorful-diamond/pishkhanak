<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiContentResource\Pages;
use App\Models\AiContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Str;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Components\Livewire;
use App\Services\AiService;
use Filament\Notifications\Notification;

class AiContentResource extends Resource
{
    protected static ?string $model = AiContent::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'AI Contents';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('ai_content.ai_contents');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('ai_content.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return __('ai_content.ai_content');
    }

    public static function getPluralModelLabel(): string
    {
        return __('ai_content.ai_contents');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Generation Information')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                Placeholder::make('model_info')
                                    ->label(__('ai_content.source_model'))
                                    ->content(function (?AiContent $record) {
                                        if (!$record || !$record->model_type) {
                                            return 'Not linked to any model';
                                        }
                                        return $record->model_type . ' #' . $record->model_id;
                                    }),
                                Placeholder::make('generation_status')
                                    ->label(__('ai_content.generation_status'))
                                    ->content(function (?AiContent $record) {
                                        if (!$record) return 'New';
                                        $status = $record->status;
                                        $progress = $record->generation_progress ?? 0;
                                        $step = $record->current_generation_step ?? '';
                                        
                                        $statusEmoji = match($status) {
                                            'generating' => '🔄',
                                            'completed' => '✅',
                                            'failed' => '❌',
                                            default => '⏳'
                                        };
                                        
                                        return "{$statusEmoji} {$status} ({$progress}%) - {$step}";
                                    }),
                                Placeholder::make('generation_time')
                                    ->label(__('ai_content.generation_time'))
                                    ->content(function (?AiContent $record) {
                                        if (!$record || !$record->generation_started_at) {
                                            return 'Not started';
                                        }
                                        
                                        $start = $record->generation_started_at;
                                        $end = $record->generation_completed_at ?? now();
                                        $duration = $start->diffForHumans($end, true);
                                        
                                        return "Started: {$start->format('Y-m-d H:i')} ({$duration})";
                                    }),
                            ]),
                    ])
                    ->visible(fn (?AiContent $record) => $record !== null)
                    ->collapsible(),
                    
                Forms\Components\Tabs::make('AI Content Tabs')
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('ai_content.basic_information'))
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label(__('ai_content.title'))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('slug')
                                    ->label(__('ai_content.slug'))
                                    ->required()
                                    ->unique(AiContent::class, 'slug', ignoreRecord: true)
                                    ->maxLength(255)
                                    ->helperText(__('ai_content.url_friendly_identifier')),
                                Forms\Components\Textarea::make('short_description')
                                    ->label(__('ai_content.short_description'))
                                    ->required()
                                    ->rows(3),
                                Forms\Components\Select::make('language')
                                    ->label(__('ai_content.language'))
                                    ->required()
                                    ->options([
                                        'Persian' => __('ai_content.persian'),
                                        'English' => __('ai_content.english'),
                                        'Arabic' => __('ai_content.arabic'),
                                        'Spanish' => __('ai_content.spanish'),
                                        'French' => __('ai_content.french'),
                                        'German' => __('ai_content.german'),
                                    ]),
                                Forms\Components\Select::make('model_type')
                                    ->label(__('ai_content.model_type'))
                                    ->required()
                                    ->options([
                                        'fast' => __('ai_content.fast'),
                                        'advanced' => __('ai_content.advanced'),
                                    ]),
                                Forms\Components\Toggle::make('status')
                                    ->label(__('ai_content.active'))
                                    ->default(true),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('ai_content.headings_sections'))
                            ->schema([
                                Forms\Components\Repeater::make('ai_headings')
                                    ->label(__('ai_content.ai_headings'))
                                    ->schema([
                                        Forms\Components\TextInput::make('title')
                                            ->label(__('ai_content.title'))
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Repeater::make('sub_headlines')
                                            ->label(__('ai_content.sub_headings'))
                                            ->schema([
                                                Forms\Components\TextInput::make('title')
                                                    ->label(__('ai_content.title'))
                                                    ->required()
                                                    ->maxLength(255),
                                            ])
                                            ->required()
                                            ->minItems(1),
                                    ])
                                    ->required()
                                    ->minItems(1),
                                ViewField::make('ai_sections')
                                    ->label(__('ai_content.ai_sections'))
                                    ->view('filament.forms.components.ai-section-editor')
                                    ->afterStateHydrated(function ($component, $state, $record) {
                                        // Pass section statuses to the view
                                        if ($record) {
                                            $component->viewData([
                                                'sectionStatuses' => $record->section_generation_status ?? []
                                            ]);
                                        }
                                    }),
                            ]),
                        Forms\Components\Tabs\Tab::make(__('ai_content.seo_meta'))
                            ->schema([
                                Forms\Components\TextInput::make('meta_title')
                                    ->label(__('ai_content.meta_title'))
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('meta_description')
                                    ->label(__('ai_content.meta_description'))
                                    ->rows(2)
                                    ->maxLength(500),
                                Forms\Components\TextInput::make('meta_keywords')
                                    ->label(__('ai_content.meta_keywords'))
                                    ->helperText(__('ai_content.comma_separated_keywords'))
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('og_title')
                                    ->label(__('ai_content.og_title'))
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('og_description')
                                    ->label(__('ai_content.og_description'))
                                    ->rows(2)
                                    ->maxLength(500),
                                Forms\Components\TextInput::make('twitter_title')
                                    ->label(__('ai_content.twitter_title'))
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('twitter_description')
                                    ->label(__('ai_content.twitter_description'))
                                    ->rows(2)
                                    ->maxLength(500),
                                Forms\Components\Textarea::make('schema')
                                    ->label(__('ai_content.json_ld_schema'))
                                    ->rows(5),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('ai_content.id'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('ai_content.title'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('ai_content.slug'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('language')
                    ->label(__('ai_content.language'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('ai_content.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'generating' => 'warning',
                        'completed' => 'success',
                        'failed' => 'danger',
                        default => 'secondary',
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('generation_progress')
                    ->label(__('ai_content.progress'))
                    ->formatStateUsing(fn ($state) => $state . '%')
                    ->sortable(),
                Tables\Columns\TextColumn::make('model_type')
                    ->label(__('ai_content.linked_to'))
                    ->formatStateUsing(fn ($record) => $record->model_type ? "{$record->model_type} #{$record->model_id}" : '-')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('ai_content.created'))
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('ai_content.last_updated'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('language')
                    ->label(__('ai_content.language'))
                    ->options([
                        'Persian' => __('ai_content.persian'),
                        'English' => __('ai_content.english'),
                        'Arabic' => __('ai_content.arabic'),
                        'Spanish' => __('ai_content.spanish'),
                        'French' => __('ai_content.french'),
                        'German' => __('ai_content.german'),
                    ]),
                Tables\Filters\TernaryFilter::make('status')
                    ->label(__('ai_content.status')),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label(__('ai_content.edit')),
                Tables\Actions\Action::make('preview')
                    ->label(__('ai_content.preview'))
                    ->url(fn ($record) => static::getUrl('preview', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
                Tables\Actions\Action::make('regenerate')
                    ->label(__('ai_content.regenerate'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->visible(fn ($record) => $record->status === 'failed' || $record->status === 'completed')
                    ->action(function ($record) {
                        $record->update([
                            'status' => 'generating',
                            'generation_progress' => 0,
                            'current_generation_step' => 'restarting',
                            'generation_started_at' => now(),
                            'generation_completed_at' => null,
                        ]);
                        
                        // Trigger regeneration job
                        // dispatch(new RegenerateAiContentJob($record));
                        
                        Notification::make()
                            ->title('Content regeneration started')
                            ->success()
                            ->send();
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label(__('ai_content.delete')),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAiContents::route('/'),
            'create' => Pages\CreateAiContent::route('/create'),
            'edit' => Pages\EditAiContent::route('/{record}/edit'),
            'preview' => Pages\PreviewAiContent::route('/{record}/preview'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
