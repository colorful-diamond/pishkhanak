<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PostResource\Pages;
use App\Models\Post;
use App\Models\User;
use App\Models\Category;
use App\Services\AiService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Archilex\AdvancedTables\AdvancedTables;
use Archilex\AdvancedTables\Filters\AdvancedFilter;
use Filament\Forms\Components\SpatieTagsInput;
use Filament\Tables\Columns\SpatieTagsColumn;
use Filament\Forms\Components\Actions\Action as FormAction;
use Filament\Actions\Action;
use Illuminate\Support\Str;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class PostResource extends Resource
{
    use AdvancedTables;

    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): ?string
    {
        return __('filament-panels::resources/post.navigation_group');
    }

    public static function getLabel(): string
    {
        return __('filament-panels::resources/post.label');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-panels::resources/post.plural_label');
    }



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('filament-panels::resources/post.sections.main_content'))
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label(__('filament-panels::resources/post.fields.title'))
                                    ->placeholder(__('filament-panels::resources/post.fields.ai_title'))
                                    ->required()
                                    ->maxLength(255)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(function (string $state, callable $set) {
                                        if($state !== '' || $state !== null){
                                            $aiSlug = app(AiService::class)->generateSlug($state);
                                            $set('slug', $aiSlug);
                                        }
                                    }),
                                Forms\Components\TextInput::make('slug')
                                    ->label(__('filament-panels::resources/post.fields.slug'))
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Post::class, 'slug', ignoreRecord: true),
                                Forms\Components\Select::make('category_id')
                                    ->label(__('filament-panels::resources/post.fields.category'))
                                    ->relationship('category', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->createOptionForm([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                        Forms\Components\Textarea::make('description')
                                            ->maxLength(1000),
                                    ]),
                                SpatieTagsInput::make('tags')
                                    ->label(__('filament-panels::resources/post.fields.tags')),
                                Forms\Components\RichEditor::make('content')
                                    ->label(__('filament-panels::resources/post.fields.content'))
                                    ->required()
                                    ->columnSpanFull(),
                                Forms\Components\RichEditor::make('summary')
                                    ->label(__('filament-panels::resources/post.fields.summary'))
                                    ->placeholder(__('filament-panels::resources/post.fields.ai_short_description'))
                                    ->columnSpanFull(),
                                Forms\Components\RichEditor::make('description')
                                    ->label(__('filament-panels::resources/post.fields.description'))
                                    ->columnSpanFull(),
                            ]),

                        Forms\Components\Section::make(__('filament-panels::resources/post.sections.media'))
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('thumbnail')
                                    ->label(__('filament-panels::resources/post.fields.thumbnail'))
                                    ->helperText(__('filament-panels::resources/post.fields.thumbnail_helper_text'))
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(5120) // 5MB
                                    ->image()
                                    ->disk('thumbnails'),
                                SpatieMediaLibraryFileUpload::make('images')
                                    ->label(__('filament-panels::resources/post.fields.images'))
                                    ->collection('gallery')
                                    ->multiple()
                                    ->reorderable()
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->maxSize(5120) // 5MB
                                    ->image()
                                    ->disk('gallery'),
                                Forms\Components\Select::make('video_type')
                                    ->label(__('filament-panels::resources/post.fields.video_type'))
                                    ->options([
                                        'local' => __('filament-panels::resources/post.fields.local'),
                                        'youtube' => __('filament-panels::resources/post.fields.youtube'),
                                        'aparat' => __('filament-panels::resources/post.fields.aparat'),
                                        'vimeo' => __('filament-panels::resources/post.fields.vimeo'),
                                    ])
                                    ->reactive(),
                                Forms\Components\TextInput::make('video')
                                    ->label(__('filament-panels::resources/post.fields.video'))
                                    ->visible(fn (callable $get) => $get('video_type') !== null)
                                    ->rule(function (callable $get) {
                                        if ($get('video_type') === 'youtube') {
                                            return ['regex:/^(https?:\/\/)?(www\.)?(youtube\.com|youtu\.?be)\/.+$/'];
                                        } elseif ($get('video_type') === 'vimeo') {
                                            return ['regex:/^(https?:\/\/)?(www\.)?(vimeo\.com)\/.+$/'];
                                        } elseif ($get('video_type') === 'aparat') {
                                            return ['regex:/^(https?:\/\/)?(www\.)?(aparat\.com)\/.+$/'];
                                        }
                                        return [];
                                    })
                                    ->helperText(function (callable $get) {
                                        if ($get('video_type') === 'local') {
                                            return __('filament-panels::resources/post.fields.local_video_helper_text');
                                        }
                                        return __('filament-panels::resources/post.fields.video_url_helper_text');
                                    }),
                            ]),

                    //         Forms\Components\Section::make(__('filament-panels::resources/post.sections.related'))
                    // ->schema([
                    //     Forms\Components\Select::make('related_services')
                    //     ->label(__('filament-panels::resources/post.fields.related_services'))
                    //     ->relationship('relatedServices', 'title')
                    //     ->multiple()
                    //     ->preload()
                    //     ->searchable(),
                    //     Forms\Components\Select::make('related_posts')
                    //         ->label(__('filament-panels::resources/post.fields.related_posts'))
                    //         ->relationship('relatedPosts', 'title')
                    //         ->multiple()
                    //         ->preload()
                    //         ->searchable(),
                    // ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Forms\Components\Group::make()
                    ->schema([
                        Forms\Components\Section::make(__('filament-panels::resources/post.sections.publishing'))
                            ->schema([
                                Forms\Components\Select::make('status')
                                    ->label(__('filament-panels::resources/post.fields.status'))
                                    ->options([
                                        'draft' => __('filament-panels::resources/post.fields.draft'),
                                        'private' => __('filament-panels::resources/post.fields.private'),
                                        'published' => __('filament-panels::resources/post.fields.published'),
                                    ])
                                    ->default('draft')
                                    ->required(),
                                Forms\Components\DateTimePicker::make('published_at')
                                    ->label(__('filament-panels::resources/post.fields.published_at'))
                                    ->nullable()->jalali(),
                                Forms\Components\Toggle::make('featured')
                                    ->label(__('filament-panels::resources/post.fields.featured')),
                                Forms\Components\Select::make('author_id')
                                    ->label(__('filament-panels::resources/post.fields.author'))
                                    ->relationship('author', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->required()
                                    ->default(auth()->id()),
                                Forms\Components\Placeholder::make('views')
                                    ->label(__('filament-panels::resources/post.fields.views'))
                                    ->content(fn (?Post $record): string => $record?->views ?? '0'),
                                Forms\Components\Placeholder::make('likes')
                                    ->label(__('filament-panels::resources/post.fields.likes'))
                                    ->content(fn (?Post $record): string => $record?->likes ?? '0'),
                                Forms\Components\Placeholder::make('shares')
                                    ->label(__('filament-panels::resources/post.fields.shares'))
                                    ->content(fn (?Post $record): string => $record?->shares ?? '0'),
                            ]),

                        Forms\Components\Section::make(__('filament-panels::resources/post.sections.seo'))
                            ->schema([
                                Forms\Components\KeyValue::make('meta')
                                    ->label(__('filament-panels::resources/post.fields.meta'))
                                    ->keyLabel(__('filament-panels::resources/post.fields.property'))
                                    ->valueLabel(__('filament-panels::resources/post.fields.value'))
                            ])
                            ->collapsible(),

                        Forms\Components\Section::make(__('filament-panels::resources/post.sections.schema'))
                            ->schema([
                                Forms\Components\KeyValue::make('schema')
                                    ->label(__('filament-panels::resources/post.fields.schema'))
                                    ->keyLabel(__('filament-panels::resources/post.fields.property'))
                                    ->valueLabel(__('filament-panels::resources/post.fields.value'))
                                    ->reorderable()
                            ])
                            ->collapsible(),
                            
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('filament-panels::resources/post.fields.id'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('title')
                    ->label(__('filament-panels::resources/post.fields.title'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('slug')
                    ->label(__('filament-panels::resources/post.fields.slug'))
                    ->searchable()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('category.name')
                    ->label(__('filament-panels::resources/post.fields.category'))
                    ->searchable()
                    ->sortable(),
                SpatieTagsColumn::make('tags')
                    ->label(__('filament-panels::resources/post.fields.tags')),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('filament-panels::resources/post.fields.status'))
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'draft' => 'gray',
                        'private' => 'warning',
                        'published' => 'success',
                    }),
                Tables\Columns\TextColumn::make('published_at')
                    ->label(__('filament-panels::resources/post.fields.published_at'))
                    ->dateTime()
                    ->jalaliDateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('featured')
                    ->label(__('filament-panels::resources/post.fields.featured'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('author.name')
                    ->label(__('filament-panels::resources/post.fields.author'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('views')
                    ->label(__('filament-panels::resources/post.fields.views'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('filament-panels::resources/post.fields.created_at'))
                    ->dateTime()
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('filament-panels::resources/post.fields.updated_at'))
                    ->dateTime()
                    ->jalaliDateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                SpatieMediaLibraryImageColumn::make('thumbnail')
                    ->label(__('filament-panels::resources/post.fields.thumbnail'))
                    ->collection('thumbnails'),
            ])
            ->filters([
                AdvancedFilter::make(),
                SelectFilter::make('category')
                    ->relationship('category', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'draft' => __('filament-panels::resources/post.fields.draft'),
                        'private' => __('filament-panels::resources/post.fields.private'),
                        'published' => __('filament-panels::resources/post.fields.published'),
                    ]),
                TernaryFilter::make('featured')
                    ->label(__('filament-panels::resources/post.fields.featured')),
                SelectFilter::make('author')
                    ->relationship('author', 'name'),
                Tables\Filters\Filter::make('published_at')
                    ->form([
                        Forms\Components\DatePicker::make('published_from')->jalali(),
                        Forms\Components\DatePicker::make('published_until')->jalali(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['published_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '>=', $date),
                            )
                            ->when(
                                $data['published_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('published_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('duplicate')
                    ->label(__('filament-panels::resources/post.actions.duplicate'))
                    ->icon('heroicon-o-document-duplicate')
                    ->action(function (Post $record) {
                        $newPost = $record->replicate();
                        $newPost->title = $newPost->title . ' (Copy)';
                        $newPost->slug = $newPost->slug . '-copy';
                        $newPost->published_at = null;
                        $newPost->views = 0;
                        $newPost->likes = 0;
                        $newPost->shares = 0;
                        $newPost->save();

                        Notification::make()
                            ->title(__('filament-panels::resources/post.notifications.post_duplicated'))
                            ->success()
                            ->send();
                    }),
            ])
            ->recordUrl(fn (Post $record): string => static::getUrl('edit', ['record' => $record]))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\BulkAction::make('updateStatus')
                        ->label(__('filament-panels::resources/post.actions.update_status'))
                        ->icon('heroicon-o-arrow-path')
                        ->requiresConfirmation()
                        ->form([
                            Forms\Components\Select::make('status')
                                ->label(__('filament-panels::resources/post.fields.status'))
                                ->options([
                                    'draft' => __('filament-panels::resources/post.fields.draft'),
                                    'private' => __('filament-panels::resources/post.fields.private'),
                                    'published' => __('filament-panels::resources/post.fields.published'),
                                ])
                                ->required(),
                        ])
                        ->action(function (Collection $records, array $data) {
                            $records->each(function ($record) use ($data) {
                                $record->update(['status' => $data['status']]);
                            });

                            Notification::make()
                                ->title(__('filament-panels::resources/post.notifications.status_updated'))
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'slug', 'content', 'summary', 'description'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Category' => $record->category->name,
            'Status' => ucfirst($record->status),
            'Published' => $record->published_at?->toFormattedDateString(),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getWidgets(): array
    {
        return [
            PostResource\Widgets\PostOverview::class,
        ];
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('generateSeoMeta')
                ->label(__('filament-panels::resources/post.actions.generate_seo_meta'))
                ->icon('heroicon-o-sparkles')
                ->action(function (Post $record, AiService $aiService) {
                    $meta = $aiService->generateMeta($record);

                    $record->update([
                        'meta' => $meta,
                    ]);

                    Notification::make()
                        ->title(__('filament-panels::resources/post.notifications.seo_meta_generated'))
                        ->success()
                        ->send();
                }),
            Action::make('generateSchema')
                ->label(__('filament-panels::resources/post.actions.generate_schema'))
                ->icon('heroicon-o-code-bracket')
                ->action(function (Post $record, AiService $aiService) {
                    $schema = $aiService->generateSchema($record);

                    $record->update([
                        'schema' => $schema['schema'],
                    ]);

                    Notification::make()
                        ->title(__('filament-panels::resources/post.notifications.schema_generated'))
                        ->success()
                        ->send();
                }),
        ];
    }
}