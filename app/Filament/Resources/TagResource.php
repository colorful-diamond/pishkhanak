<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TagResource\Pages;
use Spatie\Tags\Tag;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
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

class TagResource extends Resource
{
    use AdvancedTables; // Use the AdvancedTables trait

    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): ?string
    {
        return __('filament-panels::resources/tag.navigation_group');
    }

    public static function getLabel(): string
    {
        return __('filament-panels::resources/tag.label');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-panels::resources/tag.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make(__('filament-panels::resources/tag.tabs.tag_details'))
                    ->tabs([
                        Tab::make('Basic Info')
                            ->schema([
                                Card::make()
                                    ->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->unique(ignoreRecord: true)
                                            ->label(__('filament-panels::resources/tag.fields.name')),
                                        TextInput::make('slug')
                                            ->disabled()
                                            ->label(__('filament-panels::resources/tag.fields.slug')),
                                    ]),
                            ]),
                        Tab::make('SEO')
                            ->schema([
                                Card::make()
                                    ->schema([
                                        TextInput::make('meta_title')
                                            ->label(__('filament-panels::resources/tag.fields.meta_title'))
                                            ->maxLength(255),
                                        Textarea::make('meta_description')
                                            ->label(__('filament-panels::resources/tag.fields.meta_description'))
                                            ->rows(2),
                                        TextInput::make('meta_keywords')
                                            ->label(__('filament-panels::resources/tag.fields.meta_keywords'))
                                            ->placeholder('Comma-separated keywords'),
                                        Textarea::make('og_description')
                                            ->label(__('filament-panels::resources/tag.fields.og_description'))
                                            ->rows(2),
                                        TextInput::make('og_title')
                                            ->label(__('filament-panels::resources/tag.fields.og_title'))
                                            ->maxLength(255),
                                        Forms\Components\MediaPicker::make('og_image')
                                            ->label(__('filament-panels::resources/tag.fields.og_image'))
                                            ->directory('images/tags/og'),
                                        Textarea::make('twitter_description')
                                            ->label(__('filament-panels::resources/tag.fields.twitter_description'))
                                            ->rows(2),
                                        TextInput::make('twitter_title')
                                            ->label(__('filament-panels::resources/tag.fields.twitter_title'))
                                            ->maxLength(255),
                                        Forms\Components\MediaPicker::make('twitter_image')
                                            ->label(__('filament-panels::resources/tag.fields.twitter_image'))
                                            ->directory('images/tags/twitter'),
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
                    ->label(__('filament-panels::resources/tag.fields.slug'))
                    ->sortable()
                    ->searchable(),
                BadgeColumn::make('created_at')
                    ->label(__('filament-panels::resources/tag.fields.created_at'))
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
            'index'  => Pages\ListTags::route('/'),
            'create' => Pages\CreateTag::route('/create'),
            'edit'   => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}