<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AiSettingResource\Pages;
use App\Models\AiSetting;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Support\Enums\FontWeight;

class AiSettingResource extends Resource
{
    protected static ?string $model = AiSetting::class;

    protected static ?string $navigationIcon = 'heroicon-o-cog';
    protected static ?string $navigationGroup = 'Content Management';
    protected static ?string $navigationLabel = 'AI Settings';
    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('ai_content.ai_settings');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('ai_content.navigation_group');
    }

    public static function getModelLabel(): string
    {
        return __('ai_content.ai_settings');
    }

    public static function getPluralModelLabel(): string
    {
        return __('ai_content.ai_settings');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Tabs::make(__('ai_content.ai_settings'))
                    ->tabs([
                        Forms\Components\Tabs\Tab::make(__('ai_content.basic_settings'))
                            ->schema([
                                Forms\Components\TextInput::make('name')
                                    ->label(__('ai_content.title'))
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\Textarea::make('description')
                                    ->label(__('ai_content.short_description'))
                                    ->rows(3),
                                Forms\Components\Toggle::make('is_active')
                                    ->label(__('ai_content.active'))
                                    ->default(true),
                                Forms\Components\TextInput::make('max_tokens')
                                    ->numeric()
                                    ->default(4096)
                                    ->minValue(1)
                                    ->maxValue(65535),
                                Forms\Components\TextInput::make('temperature')
                                    ->numeric()
                                    ->default(0.7)
                                    ->minValue(0)
                                    ->maxValue(1)
                                    ->step(0.1),
                                Forms\Components\TextInput::make('frequency_penalty')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(2)
                                    ->step(0.1),
                                Forms\Components\TextInput::make('presence_penalty')
                                    ->numeric()
                                    ->default(0)
                                    ->minValue(0)
                                    ->maxValue(2)
                                    ->step(0.1),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('ai_content.model_configuration'))
                            ->schema([
                                Forms\Components\Repeater::make('model_config')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->label('Model Name'),
                                        Forms\Components\TextInput::make('model')
                                            ->required()
                                            ->label('Model Class'),
                                        Forms\Components\TextInput::make('title_field')
                                            ->required(),
                                        Forms\Components\Textarea::make('title_settings')
                                            ->required()
                                            ->label('Title Generation Settings')
                                            ->helperText('Settings for generateTitles() method'),
                                        Forms\Components\Textarea::make('section_settings')
                                            ->required()
                                            ->label('Section Generation Settings')
                                            ->helperText('Settings for generateSectionContent() method'),
                                        Forms\Components\Textarea::make('faq_settings')
                                            ->required()
                                            ->label('FAQ Generation Settings')
                                            ->helperText('Settings for generateFAQ() method'),
                                        Forms\Components\Textarea::make('summary_settings')
                                            ->required()
                                            ->label('Summary Generation Settings')
                                            ->helperText('Settings for generateSummary() method'),
                                        Forms\Components\Textarea::make('beginning_settings')
                                            ->required()
                                            ->label('Beginning Generation Settings')
                                            ->helperText('Settings for generateBeginning() method'),
                                        Forms\Components\Textarea::make('short_answers_settings')
                                            ->required()
                                            ->label('Short Answers Settings')
                                            ->helperText('Settings for generateShortAnswers() method'),
                                        Forms\Components\Textarea::make('meta_settings')
                                            ->required()
                                            ->label('Meta Generation Settings')
                                            ->helperText('Settings for generateMeta() method'),
                                        Forms\Components\Textarea::make('schema_settings')
                                            ->required()
                                            ->label('Schema Generation Settings')
                                            ->helperText('Settings for generateSchema() method'),
                                        Forms\Components\Textarea::make('humanize_settings')
                                            ->required()
                                            ->label('Content Humanization Settings')
                                            ->helperText('Settings for humanizeContent() method'),
                                        Forms\Components\TagsInput::make('searchable_fields')
                                            ->required(),
                                        Forms\Components\Select::make('model_type')
                                            ->options([
                                                'fast' => 'Fast (Gemini Flash)',
                                                'advanced' => 'Advanced (Gemini Pro)'
                                            ])
                                            ->required()
                                            ->default('fast'),
                                        Forms\Components\Toggle::make('online')
                                            ->label('Use Online Mode')
                                            ->default(false)
                                            ->helperText('Enable for real-time web search capabilities'),
                                    ])
                                    ->defaultItems(1)
                                    ->reorderable()
                                    ->collapsible(),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('ai_content.generation_settings'))
                            ->schema([
                                Forms\Components\Repeater::make('generation_settings')
                                    ->schema([
                                        Forms\Components\TextInput::make('key')
                                            ->required(),
                                        Forms\Components\TextInput::make('value')
                                            ->required(),
                                        Forms\Components\TextInput::make('description')
                                            ->required(),
                                    ])
                                    ->defaultItems(1)
                                    ->reorderable()
                                    ->collapsible(),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('ai_content.prompt_templates'))
                            ->schema([
                                Forms\Components\Repeater::make('prompt_templates')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required(),
                                        Forms\Components\Textarea::make('template')
                                            ->required()
                                            ->rows(3),
                                        Forms\Components\TagsInput::make('variables')
                                            ->required(),
                                    ])
                                    ->defaultItems(1)
                                    ->reorderable()
                                    ->collapsible(),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('ai_content.language_settings'))
                            ->schema([
                                Forms\Components\Repeater::make('language_settings')
                                    ->schema([
                                        Forms\Components\TextInput::make('code')
                                            ->required(),
                                        Forms\Components\TextInput::make('name')
                                            ->required(),
                                        Forms\Components\Toggle::make('enabled')
                                            ->default(true),
                                    ])
                                    ->defaultItems(1)
                                    ->reorderable()
                                    ->collapsible(),
                            ]),

                        Forms\Components\Tabs\Tab::make(__('ai_content.content_settings'))
                            ->schema([
                                Forms\Components\Repeater::make('tone_settings')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required(),
                                        Forms\Components\TextInput::make('description')
                                            ->required(),
                                        Forms\Components\TagsInput::make('attributes')
                                            ->required(),
                                    ])
                                    ->defaultItems(1)
                                    ->reorderable()
                                    ->collapsible(),

                                Forms\Components\Repeater::make('content_formats')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required(),
                                        Forms\Components\TextInput::make('description')
                                            ->required(),
                                        Forms\Components\TagsInput::make('structure')
                                            ->required(),
                                        Forms\Components\TextInput::make('min_words')
                                            ->numeric()
                                            ->required(),
                                        Forms\Components\TextInput::make('max_words')
                                            ->numeric()
                                            ->required(),
                                    ])
                                    ->defaultItems(1)
                                    ->reorderable()
                                    ->collapsible(),

                                Forms\Components\Repeater::make('target_audiences')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required(),
                                        Forms\Components\TextInput::make('description')
                                            ->required(),
                                        Forms\Components\TagsInput::make('characteristics')
                                            ->required(),
                                    ])
                                    ->defaultItems(1)
                                    ->reorderable()
                                    ->collapsible(),
                            ]),
                    ])
                    ->persistTabInQueryString()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold),
                Tables\Columns\TextColumn::make('description')
                    ->limit(50)
                    ->searchable(),
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TrashedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make(),
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
            'index' => Pages\ListAiSettings::route('/'),
            'create' => Pages\CreateAiSetting::route('/create'),
            'edit' => Pages\EditAiSetting::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
