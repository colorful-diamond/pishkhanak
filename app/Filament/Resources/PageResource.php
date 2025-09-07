<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Archilex\AdvancedTables\AdvancedTables; // Import AdvancedTables
use Filament\Tables\Table;
use Archilex\AdvancedTables\Filters\AdvancedFilter; // Import AdvancedFilter
use Filament\Tables\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables;

class PageResource extends Resource
{
    use AdvancedTables; // Use the AdvancedTables trait

    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document';

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): ?string
    {
        return __('filament-panels::resources/page.navigation_group');
    }

    public static function getLabel(): string
    {
        return __('filament-panels::resources/page.label');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-panels::resources/page.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make(__('filament-panels::resources/page.tabs.page_details'))
                    ->tabs([
                        Tab::make('Basic Info')
                            ->schema([
                                Card::make()
                                    ->schema([
                                        TextInput::make('title')
                                            ->required()
                                            ->maxLength(255)
                                            ->reactive()
                                            ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Str::slug($state))),
                                        TextInput::make('slug')
                                            ->disabled()
                                            ->required()
                                            ->unique(Page::class, 'slug', fn ($record) => $record),
                                        RichEditor::make('content')
                                            ->required()
                                            ->label(__('filament-panels::resources/page.fields.content')),
                                    ]),
                            ]),
                        Tab::make('SEO')
                            ->schema([
                                Card::make()
                                    ->schema([
                                        TextInput::make('meta_title')
                                            ->label(__('filament-panels::resources/page.fields.meta_title'))
                                            ->maxLength(255),
                                        RichEditor::make('meta_description')
                                            ->label(__('filament-panels::resources/page.fields.meta_description'))
                                            ->simple()
                                            ->height('100px'),
                                        TextInput::make('meta_keywords')
                                            ->label(__('filament-panels::resources/page.fields.meta_keywords'))
                                            ->placeholder('Comma-separated keywords'),
                                        RichEditor::make('og_description')
                                            ->label(__('filament-panels::resources/page.fields.og_description'))
                                            ->simple()
                                            ->height('100px'),
                                        TextInput::make('og_title')
                                            ->label(__('filament-panels::resources/page.fields.og_title'))
                                            ->maxLength(255),
                                        Forms\Components\MediaPicker::make('og_image')
                                            ->label(__('filament-panels::resources/page.fields.og_image'))
                                            ->directory('images/pages/og'),
                                        RichEditor::make('twitter_description')
                                            ->label(__('filament-panels::resources/page.fields.twitter_description'))
                                            ->simple()
                                            ->height('100px'),
                                        TextInput::make('twitter_title')
                                            ->label(__('filament-panels::resources/page.fields.twitter_title'))
                                            ->maxLength(255),
                                        Forms\Components\MediaPicker::make('twitter_image')
                                            ->label(__('filament-panels::resources/page.fields.twitter_image'))
                                            ->directory('images/pages/twitter'),
                                    ]),
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->sortable()
                    ->searchable()
                    ->limit(50),
                TextColumn::make('slug')
                    ->label(__('filament-panels::resources/page.fields.slug'))
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('created_at')
                    ->label(__('filament-panels::resources/page.fields.created_at'))
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([ // Use Advanced Filter Builder
                AdvancedFilter::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
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
            'index'  => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit'   => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}