<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Awcodes\Curator\Components\Forms\CuratorPicker;

use Filament\Forms\Form;
use Filament\Resources\Resource;
use Archilex\AdvancedTables\AdvancedTables; // Import AdvancedTables
use Archilex\AdvancedTables\Filters\AdvancedFilter; // Import AdvancedFilter
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;

class CategoryResource extends Resource
{
    use AdvancedTables; // Use the AdvancedTables trait

    protected static ?string $model = Category::class;

    protected static ?string $navigationIcon = 'heroicon-s-queue-list';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): ?string
    {
        return __('filament-panels::resources/category.navigation_group');
    }

    public static function getLabel(): string
    {
        return __('filament-panels::resources/category.label');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-panels::resources/category.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make(__('filament-panels::resources/category.tabs.category_details'))
                    ->tabs([
                        Tab::make(__('filament-panels::resources/category.tabs.category_details'))
                            ->schema([
                                Card::make()
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->reactive()
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Str::slug($state))),
                                        TextInput::make('slug')
                                            ->disabled()
                                            ->required()
                                            ->unique(Category::class, 'slug', fn ($record) => $record),
                                    ]),
                            ]),
                        Tab::make(__('filament-panels::resources/category.tabs.seo'))
                            ->schema([
                                Card::make()
                                    ->schema([
                                        TextInput::make('meta_title')
                                            ->label(__('filament-panels::resources/category.fields.meta_title'))
                                            ->maxLength(255),
                                        RichEditor::make('meta_description')
                                            ->label(__('filament-panels::resources/category.fields.meta_description')),
                                        TextInput::make('meta_keywords')
                                            ->label(__('filament-panels::resources/category.fields.meta_keywords'))
                                            ->placeholder('Comma-separated keywords'),
                                        RichEditor::make('og_description')
                                            ->label(__('filament-panels::resources/category.fields.og_description')),
                                        TextInput::make('og_title')
                                            ->label(__('filament-panels::resources/category.fields.og_title'))
                                            ->maxLength(255),
                                        CuratorPicker::make('og_image')
                                            ->label(__('filament-panels::resources/category.fields.og_image'))
                                            ->directory('images/categories/og'),
                                        RichEditor::make('twitter_description')
                                            ->label(__('filament-panels::resources/category.fields.twitter_description')),
                                        TextInput::make('twitter_title')
                                            ->label(__('filament-panels::resources/category.fields.twitter_title'))
                                            ->maxLength(255),
                                        CuratorPicker::make('twitter_image')
                                            ->label(__('filament-panels::resources/category.fields.twitter_image'))
                                            ->directory('images/categories/twitter'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->sortable()
                    ->searchable()
                    ->limit(50),
                TextColumn::make('slug')
                    ->label(__('filament-panels::resources/category.fields.slug'))
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('created_at')
                    ->label(__('filament-panels::resources/category.fields.created_at'))
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([ // Use Advanced Filter Builder
                AdvancedFilter::make(),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
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
            'index'  => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'edit'   => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}