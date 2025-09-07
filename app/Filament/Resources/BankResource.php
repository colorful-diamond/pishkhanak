<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BankResource\Pages;
use App\Models\Bank;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BankResource extends Resource
{
    protected static ?string $model = Bank::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';

    protected static ?string $navigationGroup = 'Site Management';

    public static function getPluralLabel(): ?string
    {
        return __('admin.banks');
    }

    public static function getLabel(): ?string
    {
        return __('admin.bank');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.site_management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label(__('admin.name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('en_name')
                    ->label(__('admin.en_name'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('bank_id')
                    ->label(__('admin.bank_id'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('logo')
                    ->label(__('admin.logo'))
                    ->required()
                    ->maxLength(255),
                Forms\Components\TagsInput::make('card_prefixes')
                    ->label(__('admin.card_prefixes'))
                    ->required(),
                Forms\Components\ColorPicker::make('color')
                    ->label(__('admin.color'))
                    ->required(),
                Forms\Components\Toggle::make('is_active')
                    ->label(__('admin.is_active'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('logo')->label(__('admin.logo')),
                Tables\Columns\TextColumn::make('name')->label(__('admin.name'))->searchable()->sortable(),
                Tables\Columns\TextColumn::make('en_name')->label(__('admin.en_name'))->searchable()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('bank_id')->label(__('admin.bank_id')),
                Tables\Columns\TagsColumn::make('card_prefixes')->label(__('admin.card_prefixes'))->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ColorColumn::make('color')->label(__('admin.color'))->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\ToggleColumn::make('is_active')->label(__('admin.is_active')),
                Tables\Columns\TextColumn::make('created_at')->label(__('admin.created_at'))->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')->label(__('admin.updated_at'))->dateTime()->sortable()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
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
            'index' => Pages\ListBanks::route('/'),
            'create' => Pages\CreateBank::route('/create'),
            'edit' => Pages\EditBank::route('/{record}/edit'),
        ];
    }
} 