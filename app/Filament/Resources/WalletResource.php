<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletResource\Pages;
use Bavix\Wallet\Models\Wallet;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class WalletResource extends Resource
{
    protected static ?string $model = Wallet::class;

    protected static ?string $navigationIcon = 'heroicon-o-wallet';

    protected static ?string $navigationLabel = 'کیف‌پول‌ها';

    protected static ?string $modelLabel = 'کیف‌پول';

    protected static ?string $pluralModelLabel = 'کیف‌پول‌ها';

    protected static ?string $navigationGroup = 'مدیریت مالی';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات کیف‌پول')
                    ->schema([
                        Forms\Components\Select::make('holder_id')
                            ->label('کاربر')
                            ->options(\App\Models\User::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Hidden::make('holder_type')
                            ->default(\App\Models\User::class),

                        Forms\Components\TextInput::make('name')
                            ->label('نام کیف‌پول')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('slug')
                            ->label('شناسه یکتا')
                            ->required()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),

                        Forms\Components\TextInput::make('balance')
                            ->label('موجودی (تومان)')
                            ->numeric()
                            ->default(0)
                            ->prefix('﷼'),

                        Forms\Components\KeyValue::make('meta')
                            ->label('اطلاعات اضافی')
                            ->keyLabel('کلید')
                            ->valueLabel('مقدار'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('holder.name')
                    ->label('کاربر')
                    ->searchable()
                    ->sortable()
                    ->default('بدون کاربر'),

                Tables\Columns\TextColumn::make('name')
                    ->label('نام کیف‌پول')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('slug')
                    ->label('شناسه')
                    ->searchable()
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('balance')
                    ->label('موجودی')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' تومان')
                    ->sortable()
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('IRT')
                            ->label('مجموع موجودی'),
                    ]),

                Tables\Columns\TextColumn::make('transactions_count')
                    ->label('تعداد تراکنش')
                    ->state(function (Wallet $record) {
                        try {
                            return $record->transactions()->count();
                        } catch (\Exception $e) {
                            // Log the error for debugging
                            \Illuminate\Support\Facades\Log::warning('Wallet transactions count error', [
                                'wallet_id' => $record->id,
                                'error' => $e->getMessage()
                            ]);
                            return 0;
                        }
                    })
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخرین بروزرسانی')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('holder_id')
                    ->label('کاربر')
                    ->options(\App\Models\User::pluck('name', 'id'))
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('balance')
                    ->label('موجودی')
                    ->form([
                        Forms\Components\TextInput::make('balance_from')
                            ->label('از مبلغ')
                            ->numeric(),
                        Forms\Components\TextInput::make('balance_to')
                            ->label('تا مبلغ')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['balance_from'],
                                fn (Builder $query, $value): Builder => $query->where('balance', '>=', $value),
                            )
                            ->when(
                                $data['balance_to'],
                                fn (Builder $query, $value): Builder => $query->where('balance', '<=', $value),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('refresh_balance')
                    ->label('بروزرسانی موجودی')
                    ->icon('heroicon-o-arrow-path')
                    ->action(function (Wallet $record) {
                        $record->refreshBalance();
                        
                        return redirect()->back();
                    })
                    ->successNotificationTitle('موجودی کیف‌پول بروزرسانی شد'),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('اطلاعات کیف‌پول')
                    ->schema([
                        Infolists\Components\TextEntry::make('holder.name')
                            ->label('کاربر')
                            ->default('بدون کاربر'),

                        Infolists\Components\TextEntry::make('name')
                            ->label('نام کیف‌پول'),

                        Infolists\Components\TextEntry::make('slug')
                            ->label('شناسه یکتا')
                            ->badge(),

                        Infolists\Components\TextEntry::make('balance')
                            ->label('موجودی فعلی')
                            ->money('IRT')
                            ->color('success'),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('تاریخ ایجاد')
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('آخرین بروزرسانی')
                            ->dateTime(),
                    ])->columns(2),

                Infolists\Components\Section::make('آمار تراکنش‌ها')
                    ->schema([
                        Infolists\Components\TextEntry::make('transactions_count')
                            ->label('تعداد کل تراکنش‌ها')
                            ->state(function (Wallet $record) {
                                try {
                                    return $record->transactions()->count();
                                } catch (\Exception $e) {
                                    \Illuminate\Support\Facades\Log::warning('Wallet transactions count error in infolist', [
                                        'wallet_id' => $record->id,
                                        'error' => $e->getMessage()
                                    ]);
                                    return 0;
                                }
                            }),

                        Infolists\Components\TextEntry::make('confirmed_transactions_count')
                            ->label('تراکنش‌های تایید شده')
                            ->state(function (Wallet $record) {
                                try {
                                    return $record->transactions()->where('confirmed', true)->count();
                                } catch (\Exception $e) {
                                    \Illuminate\Support\Facades\Log::warning('Wallet confirmed transactions count error', [
                                        'wallet_id' => $record->id,
                                        'error' => $e->getMessage()
                                    ]);
                                    return 0;
                                }
                            }),

                        Infolists\Components\TextEntry::make('total_deposits')
                            ->label('مجموع واریزی‌ها')
                            ->state(function (Wallet $record) {
                                try {
                                    $amount = $record->transactions()
                                        ->where('type', 'deposit')
                                        ->where('confirmed', true)
                                        ->sum('amount');
                                    return number_format($amount) . ' تومان';
                                } catch (\Exception $e) {
                                    \Illuminate\Support\Facades\Log::warning('Wallet deposits sum error', [
                                        'wallet_id' => $record->id,
                                        'error' => $e->getMessage()
                                    ]);
                                    return '0 تومان';
                                }
                            }),

                        Infolists\Components\TextEntry::make('total_withdrawals')
                            ->label('مجموع برداشت‌ها')
                            ->state(function (Wallet $record) {
                                try {
                                    $amount = $record->transactions()
                                        ->where('type', 'withdraw')
                                        ->where('confirmed', true)
                                        ->sum('amount');
                                    return number_format(abs($amount)) . ' تومان';
                                } catch (\Exception $e) {
                                    \Illuminate\Support\Facades\Log::warning('Wallet withdrawals sum error', [
                                        'wallet_id' => $record->id,
                                        'error' => $e->getMessage()
                                    ]);
                                    return '0 تومان';
                                }
                            }),
                    ])->columns(2),

                Infolists\Components\Section::make('اطلاعات اضافی')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('meta')
                            ->label('اطلاعات اضافی'),
                    ])
                    ->collapsible(),
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
            'index' => Pages\ListWallets::route('/'),
            'create' => Pages\CreateWallet::route('/create'),
            'view' => Pages\ViewWallet::route('/{record}'),
            'edit' => Pages\EditWallet::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['holder']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'slug', 'holder.name', 'holder.email'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'کاربر' => $record->holder?->name ?? 'بدون کاربر',
            'موجودی' => number_format($record->balance) . ' تومان',
        ];
    }
} 