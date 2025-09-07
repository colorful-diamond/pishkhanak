<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Components\Card;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Archilex\AdvancedTables\AdvancedTables; // Import AdvancedTables
use Filament\Tables\Table;
use Archilex\AdvancedTables\Filters\AdvancedFilter; // Import AdvancedFilter
use Filament\Tables\Actions;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tab;

class CommentResource extends Resource
{
    use AdvancedTables; // Use the AdvancedTables trait

    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-s-chat-bubble-bottom-center-text';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): ?string
    {
        return __('filament-panels::resources/comment.navigation_group');
    }

    public static function getLabel(): string
    {
        return __('filament-panels::resources/comment.label');
    }

    public static function getPluralLabel(): string
    {
        return __('filament-panels::resources/comment.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Card::make()
                    ->schema([
                        Select::make('post_id')
                            ->label(__('filament-panels::resources/comment.fields.post'))
                            ->relationship('post', 'title')
                            ->preload()
                            ->searchable()
                            ->required(),
                        TextInput::make('author_name')
                            ->label(__('filament-panels::resources/comment.fields.author_name'))
                            ->required()
                            ->maxLength(255),
                        TextInput::make('author_email')
                            ->label(__('filament-panels::resources/comment.fields.author_email'))
                            ->required()
                            ->email()
                            ->maxLength(255),
                        Textarea::make('content')
                            ->label(__('filament-panels::resources/comment.fields.content'))
                            ->required()
                            ->rows(5),
                        Textarea::make('meta_description')
                            ->label(__('filament-panels::resources/comment.fields.meta_description'))
                            ->rows(2)
                            ->helperText('Automatically filled with the first 150 characters of the content if left empty.'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('post.title')
                    ->label(__('filament-panels::resources/comment.fields.post'))
                    ->sortable()
                    ->searchable()
                    ->limit(50),
                TextColumn::make('author_name')
                    ->label(__('filament-panels::resources/comment.fields.author_name'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('author_email')
                    ->label(__('filament-panels::resources/comment.fields.author_email'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('content')
                    ->limit(50)
                    ->sortable(),
                BadgeColumn::make('created_at')
                    ->label(__('filament-panels::resources/comment.fields.created_at'))
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                AdvancedFilter::make(), // Use Advanced Filter
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
            'index'  => Pages\ListComments::route('/'),
            'create' => Pages\CreateComment::route('/create'),
            'edit'   => Pages\EditComment::route('/{record}/edit'),
        ];
    }
}