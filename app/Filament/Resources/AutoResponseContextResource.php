<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AutoResponseContextResource\Pages;
use App\Models\AutoResponseContext;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Traits\HasResourcePermissions;

class AutoResponseContextResource extends Resource
{
    use HasResourcePermissions;

    protected static ?string $model = AutoResponseContext::class;

    protected static ?string $navigationGroup = 'PERSIAN_TEXT_4efd4a96';

    protected static ?string $navigationLabel = 'PERSIAN_TEXT_e12561f6';

    protected static ?string $modelLabel = 'PERSIAN_TEXT_33abbc29';

    protected static ?string $pluralModelLabel = 'PERSIAN_TEXT_e12561f6';

    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('PERSIAN_TEXT_c303ed90')
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->label('PERSIAN_TEXT_d809ff6f')
                            ->required()
                            ->maxLength(255)
                            ->helperText('PERSIAN_TEXT_b661a978'),

                        Forms\Components\Textarea::make('description')
                            ->label('PERSIAN_TEXT_86f150b8')
                            ->rows(3)
                            ->maxLength(1000)
                            ->helperText('PERSIAN_TEXT_359c1a2f'),

                        Forms\Components\Textarea::make('keywords')
                            ->label('PERSIAN_TEXT_266c621c')
                            ->rows(2)
                            ->helperText('PERSIAN_TEXT_81af963d')
                            ->placeholder('PERSIAN_TEXT_1a3feb37'),

                        Forms\Components\Textarea::make('example_queries')
                            ->label('PERSIAN_TEXT_d974b349')
                            ->rows(4)
                            ->helperText('PERSIAN_TEXT_60672c73')
                            ->placeholder("PERSIAN_TEXT_c1045f74"),
                    ])
                    ->columns(1),

                Forms\Components\Section::make('PERSIAN_TEXT_b67081a5')
                    ->schema([
                        Forms\Components\Toggle::make('is_active')
                            ->label('PERSIAN_TEXT_25c499f4')
                            ->default(true)
                            ->helperText('PERSIAN_TEXT_1d993084'),

                        Forms\Components\TextInput::make('priority')
                            ->label('PERSIAN_TEXT_44e7afcf')
                            ->numeric()
                            ->default(0)
                            ->minValue(0)
                            ->maxValue(999)
                            ->helperText('PERSIAN_TEXT_6d663cc3'),

                        Forms\Components\TextInput::make('confidence_threshold')
                            ->label('PERSIAN_TEXT_332a01e4')
                            ->numeric()
                            ->default(0.7)
                            ->minValue(0)
                            ->maxValue(1)
                            ->step(0.1)
                            ->helperText('PERSIAN_TEXT_545b924e'),
                    ])
                    ->columns(3),

                Forms\Components\Section::make('PERSIAN_TEXT_781de818')
                    ->schema([
                        Forms\Components\Placeholder::make('effectiveness_score')
                            ->label('PERSIAN_TEXT_79babdd9')
                            ->content(fn ($record) => $record?->effectiveness_score !== null 
                                ? $record->effectiveness_score . '%' 
                                : 'PERSIAN_TEXT_e4966467'),

                        Forms\Components\Placeholder::make('total_uses')
                            ->label('PERSIAN_TEXT_51b4a52e')
                            ->content(fn ($record) => $record?->logs()->count() ?? 0),

                        Forms\Components\Placeholder::make('created_at')
                            ->label('PERSIAN_TEXT_50148776')
                            ->content(fn ($record) => $record?->created_at?->diffForHumans() ?? '-'),
                    ])
                    ->columns(3)
                    ->hidden(fn ($operation) => $operation === 'create'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('PERSIAN_TEXT_09b003f0')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('PERSIAN_TEXT_86f150b8')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description),

                Tables\Columns\TextColumn::make('responses_count')
                    ->label('PERSIAN_TEXT_88be6a62')
                    ->counts('responses')
                    ->badge()
                    ->color('success'),

                Tables\Columns\TextColumn::make('priority')
                    ->label('PERSIAN_TEXT_44e7afcf')
                    ->sortable()
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 100 => 'danger',
                        $state >= 50 => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('confidence_threshold')
                    ->label('PERSIAN_TEXT_332a01e4')
                    ->formatStateUsing(fn ($state) => number_format($state * 100, 0) . '%')
                    ->badge()
                    ->color('info'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('PERSIAN_TEXT_2f3c6cf1')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                Tables\Columns\TextColumn::make('logs_count')
                    ->label('PERSIAN_TEXT_9c8f9078')
                    ->counts('logs')
                    ->badge(),

                Tables\Columns\TextColumn::make('effectiveness_score')
                    ->label('PERSIAN_TEXT_a1d7d6a7')
                    ->formatStateUsing(fn ($state) => $state !== null ? $state . '%' : '-')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state >= 80 => 'success',
                        $state >= 60 => 'warning',
                        $state !== null => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('PERSIAN_TEXT_50148776')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('PERSIAN_TEXT_2f3c6cf1')
                    ->placeholder('PERSIAN_TEXT_fe641855')
                    ->trueLabel('PERSIAN_TEXT_25c499f4')
                    ->falseLabel('PERSIAN_TEXT_7fdadc73'),

                Tables\Filters\Filter::make('has_responses')
                    ->label('PERSIAN_TEXT_ab88904a')
                    ->query(fn (Builder $query) => $query->has('responses')),

                Tables\Filters\Filter::make('high_priority')
                    ->label('PERSIAN_TEXT_d8a76433')
                    ->query(fn (Builder $query) => $query->where('priority', '>=', 50)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\Action::make('manage_responses')
                    ->label('PERSIAN_TEXT_9d00eefd')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->url(fn ($record) => AutoResponseResource::getUrl('index', ['context' => $record->id]))
                    ->color('success'),
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
            ->defaultSort('priority', 'desc');
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
            'index' => Pages\ListAutoResponseContexts::route('/'),
            'create' => Pages\CreateAutoResponseContext::route('/create'),
            'view' => Pages\ViewAutoResponseContext::route('/{record}'),
            'edit' => Pages\EditAutoResponseContext::route('/{record}/edit'),
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
}
