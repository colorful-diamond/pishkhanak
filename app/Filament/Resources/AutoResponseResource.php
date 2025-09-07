<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AutoResponseResource\Pages;
use App\Models\AutoResponse;
use App\Models\AutoResponseContext;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasResourcePermissions;

class AutoResponseResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = AutoResponse::class;

    protected static ?string $navigationGroup = 'PERSIAN_TEXT_4efd4a96';

    protected static ?string $navigationLabel = 'PERSIAN_TEXT_3abb6841';

    protected static ?string $modelLabel = 'PERSIAN_TEXT_dd57f89a';

    protected static ?string $pluralModelLabel = 'PERSIAN_TEXT_3abb6841';

    protected static ?int $navigationSort = 5;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('PERSIAN_TEXT_95d00d59')
                    ->schema([
                        Forms\Components\Select::make('context_id')
                            ->label('PERSIAN_TEXT_0e9923cf')
                            ->relationship('context', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->helperText('PERSIAN_TEXT_9fba795e'),

                        Forms\Components\TextInput::make('title')
                            ->label('PERSIAN_TEXT_34d19e14')
                            ->required()
                            ->maxLength(255)
                            ->helperText('PERSIAN_TEXT_d4680f3e'),

                        Forms\Components\Select::make('language')
                            ->label('PERSIAN_TEXT_4a8c7291')
                            ->options([
                                'fa' => 'PERSIAN_TEXT_66030b73',
                                'en' => 'PERSIAN_TEXT_36a74d2f',
                            ])
                            ->default('fa')
                            ->required()
                            ->helperText('PERSIAN_TEXT_b3f03748'),

                        Forms\Components\RichEditor::make('response_text')
                            ->label('PERSIAN_TEXT_8bdb11b4')
                            ->required()
                            ->columnSpanFull()
                            ->helperText('PERSIAN_TEXT_17d0a196'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('PERSIAN_TEXT_327927bc')
                    ->schema([
                        Forms\Components\Repeater::make('attachments')
                            ->label('PERSIAN_TEXT_ed7a12d7')
                            ->schema([
                                Forms\Components\FileUpload::make('file_path')
                                    ->label('PERSIAN_TEXT_a0bb1346')
                                    ->directory('auto-responses')
                                    ->required(),
                                Forms\Components\TextInput::make('description')
                                    ->label('PERSIAN_TEXT_86f150b8')
                                    ->maxLength(255),
                            ])
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['description'] ??'PERSIAN_TEXT_02b25ec3')
                            ->addActionLabel('PERSIAN_TEXT_fe924973'),

                        Forms\Components\Repeater::make('links')
                            ->label('PERSIAN_TEXT_e7fa644e')
                            ->schema([
                                Forms\Components\TextInput::make('title')
                                    ->label('PERSIAN_TEXT_5ee32e01')
                                    ->required()
                                    ->maxLength(255),
                                Forms\Components\TextInput::make('url')
                                    ->label('PERSIAN_TEXT_a98dd4d9')
                                    ->url()
                                    ->required()
                                    ->maxLength(500),
                            ])
                            ->collapsed()
                            ->itemLabel(fn (array $state): ?string => $state['title'] ??'PERSIAN_TEXT_0be0b678')
                            ->addActionLabel('PERSIAN_TEXT_e52fbbd7'),
                    ]),

                Forms\Components\Section::make('PERSIAN_TEXT_b67081a5')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('PERSIAN_TEXT_25c499f4')
                            ->default(true)
                            ->helperText('PERSIAN_TEXT_6daa1034'),

                        Forms\Components\Toggle::make('mark_as_resolved')
                            ->label('PERSIAN_TEXT_338503da')
                            ->default(false)
                            ->helperText('PERSIAN_TEXT_5dcd43ea'),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('PERSIAN_TEXT_26a8e5c0')
                    ->schema([
                        Forms\Components\Placeholder::make('usage_count')
                            ->label('PERSIAN_TEXT_51b4a52e')
                            ->content(fn ($record) => $record?->usage_count ?? 0),

                        Forms\Components\Placeholder::make('satisfaction_score')
                            ->label('PERSIAN_TEXT_1df0b118')
                            ->content(fn ($record) => $record?->satisfaction_score 
                                ? number_format($record->satisfaction_score, 1) . 'PERSIAN_TEXT_3fc892a6' 
                                : 'PERSIAN_TEXT_e4966467'),

                        Forms\Components\Placeholder::make('effectiveness_percentage')
                            ->label('PERSIAN_TEXT_9c17aedb')
                            ->content(fn ($record) => $record?->effectiveness_percentage !== null
                                ? $record->effectiveness_percentage . '%'
                                : 'PERSIAN_TEXT_e4966467'),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('PERSIAN_TEXT_50148776')
                            ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? '-'),
                    ])
                    ->columns(4)
                    ->hidden(fn ($operation) => $operation === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('context.name')
                    ->label('PERSIAN_TEXT_0e9923cf')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),

                Tables\Columns\TextColumn::make('title')
                    ->label('PERSIAN_TEXT_ca970034')
                    ->searchable()
                    ->limit(30),

                Tables\Columns\TextColumn::make('language')
                    ->label('PERSIAN_TEXT_4a8c7291')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'fa' => 'PERSIAN_TEXT_66030b73',
                        'en' => 'English',
                        default => $state,
                    }),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('PERSIAN_TEXT_25c499f4')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\IconColumn::make('mark_as_resolved')
                    ->label('PERSIAN_TEXT_1bbccabe')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-minus-circle')
                    ->tooltip(fn ($state) => $state 
                        ? 'PERSIAN_TEXT_c2c92a42'
                        : 'PERSIAN_TEXT_e7949096'),

                Tables\Columns\TextColumn::make('usage_count')
                    ->label('PERSIAN_TEXT_9c8f9078')
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('satisfaction_score')
                    ->label('PERSIAN_TEXT_202133ba')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state !== null 
                        ? number_format($state, 1) 
                        : '-')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 4 => 'success',
                        $state >= 3 => 'warning',
                        $state !== null => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('effectiveness_percentage')
                    ->label('PERSIAN_TEXT_f80d69bb')
                    ->formatStateUsing(fn ($state) => $state !== null ? $state . '%' : '-')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        $state !== null => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('PERSIAN_TEXT_66fa013c')
                    ->dateTime('Y/m/d')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('context_id')
                    ->label('PERSIAN_TEXT_0e9923cf')
                    ->relationship('context', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('language')
                    ->label('PERSIAN_TEXT_4a8c7291')
                    ->options([
                        'fa' => 'PERSIAN_TEXT_66030b73',
                        'en' => 'PERSIAN_TEXT_36a74d2f',
                    ]),

                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('PERSIAN_TEXT_88bd5656')
                    ->placeholder('PERSIAN_TEXT_fe641855')
                    ->trueLabel('PERSIAN_TEXT_25c499f4')
                    ->falseLabel('PERSIAN_TEXT_7fdadc73'),

                Tables\Filters\TernaryFilter::make('mark_as_resolved')
                    ->label('PERSIAN_TEXT_669a9b04')
                    ->placeholder('PERSIAN_TEXT_fe641855')
                    ->trueLabel('PERSIAN_TEXT_5e8fdd3b')
                    ->falseLabel('PERSIAN_TEXT_36f5ebad'),

                Tables\Filters\Filter::make('high_usage')
                    ->label('PERSIAN_TEXT_b1de14a8')
                    ->query(fn (Builder $query) => $query->where('usage_count', '>=', 10)),

                Tables\Filters\Filter::make('low_satisfaction')
                    ->label('PERSIAN_TEXT_82cc1645')
                    ->query(fn (Builder $query) => $query->where('satisfaction_score', '<', 3)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('preview')
                    ->label('PERSIAN_TEXT_f07d7cd0')
                    ->icon('heroicon-o-eye')
                    ->modalHeading('PERSIAN_TEXT_62fa7e3c')
                    ->modalContent(fn ($record) => view('filament.resources.auto-response.preview', ['response' => $record]))
                    ->modalSubmitAction(false),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('activate')
                        ->label('PERSIAN_TEXT_9947261b')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => true])),
                    Tables\Actions\BulkAction::make('deactivate')
                        ->label('PERSIAN_TEXT_771a0789')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn ($records) => $records->each->update(['is_active' => false])),
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
            'index' => Pages\ListAutoResponses::route('/'),
            'create' => Pages\CreateAutoResponse::route('/create'),
            'view' => Pages\ViewAutoResponse::route('/{record}'),
            'edit' => Pages\EditAutoResponse::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'success';
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // If context filter is set in the URL, apply it
        if (request()->has('context')) {
            $query->where('context_id', request('context'));
        }

        return $query;
    }
}
