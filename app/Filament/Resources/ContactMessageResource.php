<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactMessageResource\Pages;
use App\Filament\Resources\ContactMessageResource\RelationManagers;
use App\Models\ContactMessage;
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Site Management';
    protected static ?int $navigationSort = 3;

    public static function getPluralLabel(): ?string
    {
        return __('admin.contact_messages');
    }

    public static function getLabel(): ?string
    {
        return __('admin.contact_message');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.site_management');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->label(__('forms.labels.name'))
                    ->disabled()
                    ->columnSpanFull(),
                TextInput::make('email')
                    ->label(__('forms.labels.email'))
                    ->email()
                    ->disabled()
                    ->columnSpanFull(),
                TextInput::make('subject')
                    ->label(__('forms.labels.subject'))
                    ->disabled()
                    ->columnSpanFull(),
                Textarea::make('message')
                    ->label(__('forms.labels.message'))
                    ->disabled()
                    ->rows(6)
                    ->columnSpanFull(),
                Toggle::make('is_read')
                    ->label(__('forms.labels.mark_as_read'))
                    ->onIcon('heroicon-s-eye')
                    ->offIcon('heroicon-s-eye-slash'),
                Placeholder::make('created_at')
                    ->label(__('forms.labels.received_at'))
                    ->content(fn (?ContactMessage $record): string => $record?->created_at?->diffForHumans() ?? '-')
                    ->columnSpanFull(),
                Placeholder::make('updated_at')
                    ->label(__('forms.labels.last_updated_at'))
                    ->content(fn (?ContactMessage $record): string => $record?->updated_at?->diffForHumans() ?? '-')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('admin.contact_name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label(__('admin.contact_email'))
                    ->searchable(),
                TextColumn::make('subject')
                    ->label(__('admin.contact_subject'))
                    ->searchable()
                    ->limit(50),
                IconColumn::make('is_read')
                    ->label(__('tables.columns.status'))
                    ->boolean()
                    ->trueIcon('heroicon-s-eye')
                    ->falseIcon('heroicon-s-eye-slash')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('admin.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_read')
                    ->label(__('tables.filters.status'))
                    ->boolean()
                    ->trueLabel(__('tables.filters.read'))
                    ->falseLabel(__('tables.filters.unread'))
                    ->placeholder(__('tables.filters.all')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Action::make('toggleRead')
                    ->label(fn (ContactMessage $record): string => $record->is_read ? __('tables.actions.mark_as_unread') : __('tables.actions.mark_as_read'))
                    ->icon(fn (ContactMessage $record): string => $record->is_read ? 'heroicon-s-eye-slash' : 'heroicon-s-eye')
                    ->action(function (ContactMessage $record) {
                        $record->is_read = !$record->is_read;
                        $record->save();
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            // No relations needed for now
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            // 'create' => Pages\CreateContactMessage::route('/create'), // Users submit, admins don't create
            'view' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }
}
