<?php

namespace App\Filament\Resources;

use App\Filament\Resources\WalletTransactionResource\Pages;
use Bavix\Wallet\Models\Transaction;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\TextColumn;

class WalletTransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationLabel = 'تراکنش‌های کیف‌پول';

    protected static ?string $modelLabel = 'تراکنش';

    protected static ?string $pluralModelLabel = 'تراکنش‌ها';

    protected static ?string $navigationGroup = 'مدیریت مالی';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('اطلاعات تراکنش')
                    ->schema([
                        Forms\Components\Select::make('wallet_id')
                            ->label('کیف‌پول')
                            ->options(function () {
                                return \Bavix\Wallet\Models\Wallet::with('holder')
                                    ->get()
                                    ->mapWithKeys(function ($wallet) {
                                        $holderName = $wallet->holder ? $wallet->holder->name : 'نامشخص';
                                        return [$wallet->id => $holderName . ' - ' . $wallet->uuid];
                                    });
                            })
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\Select::make('type')
                            ->label('نوع تراکنش')
                            ->options([
                                'deposit' => 'واریز',
                                'withdraw' => 'برداشت',
                                'transfer' => 'انتقال',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('amount')
                            ->label('مبلغ (تومان)')
                            ->numeric()
                            ->required()
                            ->prefix('تومان'),

                        Forms\Components\Toggle::make('confirmed')
                            ->label('تایید شده')
                            ->default(true),

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
                Tables\Columns\TextColumn::make('wallet.holder.name')
                    ->label('کاربر')
                    ->searchable()
                    ->sortable()
                    ->default('نامشخص'),

                Tables\Columns\TextColumn::make('wallet.uuid')
                    ->label('کیف‌پول')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(fn ($state, $record) => $record->wallet?->holder?->name ?? 'نامشخص'),

                Tables\Columns\TextColumn::make('type')
                    ->label('نوع تراکنش')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'deposit' => 'واریز',
                        'withdraw' => 'برداشت',
                        'transfer' => 'انتقال',
                        default => $state
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'deposit' => 'success',
                        'withdraw' => 'danger',
                        'transfer' => 'info',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('amount')
                    ->label('مبلغ')
                    ->formatStateUsing(fn ($state) => number_format(abs($state)) . ' تومان')
                    ->sortable(),

                Tables\Columns\TextColumn::make('meta.source_tracking.source_title')
                    ->label('منبع پرداخت')
                    ->formatStateUsing(function ($state, $record) {
                        $meta = $record->meta ?? [];
                        
                        // ALWAYS prioritize service ID for reliable identification
                        // This fixes the sub-service slug duplication issue
                        $serviceId = $meta['source_tracking']['source_id'] ?? 
                                   $meta['service_id'] ?? 
                                   null;
                                   
                        if ($serviceId) {
                            $service = \App\Models\Service::find($serviceId);
                            if ($service) {
                                return $service->title;
                            }
                        }
                        
                        // Fallback to stored title if service not found by ID
                        $serviceTitle = $meta['source_tracking']['source_title'] ?? 
                                      $meta['service_title'] ?? 
                                      $meta['service_name'] ?? 
                                      null;
                        
                        if ($serviceTitle) {
                            return $serviceTitle;
                        }
                        
                        // Check transaction type and meta for context
                        $type = $meta['type'] ?? '';
                        if (in_array($type, ['wallet_charge', 'wallet_charge_for_service'])) {
                            return 'شارژ کیف‌پول';
                        } elseif ($type === 'service_payment') {
                            return 'پرداخت سرویس';
                        }
                        
                        return 'نامشخص';
                    })
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('meta.source_tracking.source_category')
                    ->label('دسته‌بندی منبع')
                    ->searchable()
                    ->sortable()
                    ->default('بدون دسته‌بندی')
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('meta.source_tracking.payment_flow')
                    ->label('جریان پرداخت')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'wallet_balance' => 'موجودی کیف‌پول',
                        'gateway_to_wallet' => 'درگاه به کیف‌پول',
                        'wallet_payment_after_gateway_charge' => 'پرداخت کیف‌پول بعد از شارژ',
                        'admin_to_wallet' => 'ادمین به کیف‌پول',
                        'wallet_to_admin' => 'کیف‌پول به ادمین',
                        'wallet_to_admin_forced' => 'کسر اجباری',
                        default => $state ?? 'نامشخص'
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'wallet_balance' => 'warning',
                        'gateway_to_wallet' => 'success',
                        'wallet_payment_after_gateway_charge' => 'info',
                        'admin_to_wallet' => 'primary',
                        'wallet_to_admin' => 'danger',
                        'wallet_to_admin_forced' => 'danger',
                        default => 'gray'
                    }),

                Tables\Columns\TextColumn::make('meta.source_tracking.transaction_context')
                    ->label('زمینه تراکنش')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'service_preview_payment' => 'پرداخت از پیش‌نمایش سرویس',
                        'wallet_charge_for_service_continuation' => 'شارژ کیف‌پول برای ادامه سرویس',
                        'service_payment_after_wallet_charge' => 'پرداخت سرویس بعد از شارژ',
                        'manual_wallet_charge' => 'شارژ دستی کیف‌پول',
                        'admin_wallet_charge' => 'شارژ توسط ادمین',
                        'admin_wallet_deduction' => 'کسر توسط ادمین',
                        'admin_wallet_force_deduction' => 'کسر اجباری',
                        default => $state ?? 'نامشخص'
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\IconColumn::make('confirmed')
                    ->label('تایید شده')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime()
                    ->sortable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('آخرین بروزرسانی')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('wallet.holder')
                    ->label('کاربر')
                    ->relationship('wallet.holder', 'name')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('wallet')
                    ->label('کیف‌پول')
                    ->options(function () {
                        return \Bavix\Wallet\Models\Wallet::with('holder')
                            ->get()
                            ->mapWithKeys(function ($wallet) {
                                $holderName = $wallet->holder ? $wallet->holder->name : 'نامشخص';
                                return [$wallet->id => $holderName . ' - ' . $wallet->uuid];
                            });
                    })
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('type')
                    ->label('نوع تراکنش')
                    ->options([
                        'deposit' => 'واریز',
                        'withdraw' => 'برداشت',
                        'transfer' => 'انتقال',
                    ]),

                Tables\Filters\SelectFilter::make('meta.source_tracking.source_type')
                    ->label('نوع منبع')
                    ->options([
                        'service' => 'سرویس',
                        'admin_action' => 'عملیات ادمین',
                        'manual_wallet_charge' => 'شارژ دستی',
                        'gateway_payment' => 'پرداخت درگاه',
                    ]),

                Tables\Filters\SelectFilter::make('meta.source_tracking.payment_flow')
                    ->label('جریان پرداخت')
                    ->options([
                        'wallet_balance' => 'موجودی کیف‌پول',
                        'gateway_to_wallet' => 'درگاه به کیف‌پول',
                        'wallet_payment_after_gateway_charge' => 'پرداخت کیف‌پول بعد از شارژ',
                        'admin_to_wallet' => 'ادمین به کیف‌پول',
                        'wallet_to_admin' => 'کیف‌پول به ادمین',
                        'wallet_to_admin_forced' => 'کسر اجباری',
                    ]),

                Tables\Filters\Filter::make('service_payments')
                    ->label('پرداخت‌های سرویس')
                    ->query(function (Builder $query): Builder {
                        return $query->whereNotNull('meta->source_tracking->source_id')
                            ->where('meta->source_tracking->source_type', 'service');
                    })
                    ->toggle(),

                Tables\Filters\Filter::make('admin_actions')
                    ->label('عملیات‌های ادمین')
                    ->query(function (Builder $query): Builder {
                        return $query->where('meta->source_tracking->source_type', 'admin_action');
                    })
                    ->toggle(),

                Tables\Filters\TernaryFilter::make('confirmed')
                    ->label('وضعیت تایید')
                    ->trueLabel('تایید شده')
                    ->falseLabel('تایید نشده')
                    ->native(false),

                Tables\Filters\Filter::make('date_range')
                    ->label('محدوده تاریخ')
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label('از تاریخ'),
                        Forms\Components\DatePicker::make('created_until')
                            ->label('تا تاریخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),

                Tables\Filters\Filter::make('amount_range')
                    ->label('محدوده مبلغ')
                    ->form([
                        Forms\Components\TextInput::make('amount_from')
                            ->label('از مبلغ')
                            ->numeric(),
                        Forms\Components\TextInput::make('amount_to')
                            ->label('تا مبلغ')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['amount_from'],
                                fn (Builder $query, $value): Builder => $query->where('amount', '>=', $value),
                            )
                            ->when(
                                $data['amount_to'],
                                fn (Builder $query, $value): Builder => $query->where('amount', '<=', $value),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\Action::make('toggle_confirmation')
                    ->label(fn (Transaction $record): string => $record->confirmed ? 'لغو تایید' : 'تایید')
                    ->icon(fn (Transaction $record): string => $record->confirmed ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (Transaction $record): string => $record->confirmed ? 'danger' : 'success')
                    ->action(function (Transaction $record) {
                        $record->update(['confirmed' => !$record->confirmed]);
                    })
                    ->successNotificationTitle('وضعیت تراکنش بروزرسانی شد'),

                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('confirm')
                        ->label('تایید انبوه')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->action(function ($records) {
                            $records->each->update(['confirmed' => true]);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('تراکنش‌های انتخاب شده تایید شدند'),

                    Tables\Actions\BulkAction::make('reject')
                        ->label('لغو تایید انبوه')
                        ->icon('heroicon-o-x-mark')
                        ->color('danger')
                        ->action(function ($records) {
                            $records->each->update(['confirmed' => false]);
                        })
                        ->deselectRecordsAfterCompletion()
                        ->successNotificationTitle('تایید تراکنش‌های انتخاب شده لغو شد'),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('اطلاعات تراکنش')
                    ->schema([
                        Infolists\Components\TextEntry::make('user.name')
                            ->label('کاربر'),

                        Infolists\Components\TextEntry::make('wallet.name')
                            ->label('کیف‌پول'),

                        Infolists\Components\TextEntry::make('type_text')
                            ->label('نوع تراکنش')
                            ->badge()
                            ->color(fn (Transaction $record): string => match ($record->type) {
                                'deposit' => 'success',
                                'withdraw' => 'danger',
                                'transfer' => 'info',
                                default => 'gray',
                            }),

                        Infolists\Components\TextEntry::make('formatted_amount')
                            ->label('مبلغ')
                            ->color(fn (Transaction $record): string => $record->amount >= 0 ? 'success' : 'danger'),

                        Infolists\Components\TextEntry::make('status')
                            ->label('وضعیت')
                            ->badge()
                            ->color(fn (Transaction $record): string => $record->confirmed ? 'success' : 'warning'),

                        Infolists\Components\TextEntry::make('uuid')
                            ->label('شناسه یکتا')
                            ->copyable(),

                        Infolists\Components\TextEntry::make('created_at')
                            ->label('تاریخ ایجاد')
                            ->dateTime(),

                        Infolists\Components\TextEntry::make('updated_at')
                            ->label('آخرین بروزرسانی')
                            ->dateTime(),
                    ])->columns(2),

                Infolists\Components\Section::make('اطلاعات اضافی')
                    ->schema([
                        Infolists\Components\KeyValueEntry::make('meta')
                            ->label('اطلاعات اضافی'),
                    ])
                    ->collapsible()
                    ->collapsed(),
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
            'index' => Pages\ListWalletTransactions::route('/'),
            'create' => Pages\CreateWalletTransaction::route('/create'),
            'view' => Pages\ViewWalletTransaction::route('/{record}'),
            'edit' => Pages\EditWalletTransaction::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'wallet']);
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['uuid', 'user.name', 'user.email', 'wallet.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'کاربر' => $record->user->name,
            'کیف‌پول' => $record->wallet->name,
            'مبلغ' => $record->formatted_amount,
        ];
    }
} 