<?php

namespace App\Filament\Resources;

use App\Filament\Resources\GatewayTransactionResource\Pages;
use App\Models\Currency;
use App\Models\PaymentGateway;
use App\Models\GatewayTransaction;
use App\Models\User;
use Archilex\AdvancedTables\AdvancedTables;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Grid;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\DateFilter;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use App\Services\PaymentGatewayManager;

class GatewayTransactionResource extends Resource
{
    use AdvancedTables;
    
    protected static ?string $model = GatewayTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-refund';
    
    protected static ?string $navigationLabel = 'تراکنش‌های پرداخت';
    
    protected static ?string $modelLabel = 'تراکنش پرداخت';
    
    protected static ?string $pluralModelLabel = 'تراکنش‌های پرداخت';

    protected static ?string $navigationGroup = 'پرداخت';

    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('اطلاعات تراکنش')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('uuid')
                                    ->label('شناسه یکتا')
                                    ->disabled()
                                    ->dehydrated(false),

                                TextInput::make('gateway_transaction_id')
                                    ->label('شناسه تراکنش درگاه')
                                    ->maxLength(255),

                                Select::make('user_id')
                                    ->label('کاربر')
                                    ->relationship('user', 'name')
                                    ->searchable()
                                    ->required(),

                                Select::make('payment_gateway_id')
                                    ->label('درگاه پرداخت')
                                    ->relationship('paymentGateway', 'name')
                                    ->searchable()
                                    ->required(),

                                Select::make('currency_id')
                                    ->label('ارز')
                                    ->relationship('currency', 'name')
                                    ->searchable()
                                    ->required(),

                                Select::make('type')
                                    ->label('نوع تراکنش')
                                    ->options([
                                        'payment' => 'پرداخت',
                                        'refund' => 'بازگشت وجه',
                                        'partial_refund' => 'بازگشت جزئی',
                                    ])
                                    ->default('payment')
                                    ->required(),

                                Select::make('status')
                                    ->label('وضعیت')
                                    ->options([
                                        'pending' => 'در انتظار',
                                        'processing' => 'در حال پردازش',
                                        'completed' => 'تکمیل شده',
                                        'failed' => 'ناموفق',
                                        'cancelled' => 'لغو شده',
                                        'refunded' => 'بازگشت شده',
                                        'partially_refunded' => 'بازگشت جزئی',
                                        'expired' => 'منقضی شده',
                                    ])
                                    ->default('pending')
                                    ->required(),
                            ]),

                        Grid::make(2)
                            ->schema([
                                TextInput::make('amount')
                                    ->label('مبلغ اصلی (تومان)')
                                    ->numeric()
                                    ->required()
                                    ->helperText('مبلغ اصلی تراکنش'),

                                TextInput::make('tax_amount')
                                    ->label('مبلغ مالیات (تومان)')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('مبلغ مالیات محاسبه شده'),

                                TextInput::make('gateway_fee')
                                    ->label('کارمزد درگاه (تومان)')
                                    ->numeric()
                                    ->default(0)
                                    ->helperText('کارمزد درگاه پرداخت'),

                                TextInput::make('total_amount')
                                    ->label('مبلغ کل (تومان)')
                                    ->numeric()
                                    ->required()
                                    ->helperText('مجموع مبلغ اصلی، مالیات و کارمزد'),
                            ]),

                        Textarea::make('description')
                            ->label('توضیحات')
                            ->maxLength(1000)
                            ->rows(3)
                            ->placeholder('توضیحات تراکنش'),
                    ])
                    ->collapsible(),

                Section::make('اطلاعات کاربر')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('user_ip')
                                    ->label('آدرس IP کاربر')
                                    ->maxLength(45),

                                TextInput::make('user_country')
                                    ->label('کشور کاربر')
                                    ->maxLength(2),

                                TextInput::make('user_device')
                                    ->label('دستگاه کاربر')
                                    ->maxLength(255),
                            ]),

                        Textarea::make('user_agent')
                            ->label('User Agent')
                            ->rows(2)
                            ->maxLength(1000),
                    ])
                    ->collapsible(),

                Section::make('اطلاعات درگاه')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('gateway_reference')
                                    ->label('مرجع درگاه')
                                    ->maxLength(255),

                                TextInput::make('gateway_response')
                                    ->label('پاسخ درگاه')
                                    ->disabled()
                                    ->dehydrated(false),
                            ]),
                    ])
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('uuid')
                    ->label('شناسه')
                    ->searchable()
                    ->copyable()
                    ->limit(10)
                    ->tooltip(function ($record) {
                        return $record->uuid;
                    }),

                TextColumn::make('user.name')
                    ->label('کاربر')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('paymentGateway.name')
                    ->label('درگاه')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),

                TextColumn::make('amount')
                    ->label('مبلغ')
                    ->formatStateUsing(function ($record) {
                        return number_format($record->amount) . ' تومان';
                    })
                    ->sortable(),

                TextColumn::make('total_amount')
                    ->label('مبلغ کل')
                    ->formatStateUsing(function ($record) {
                        return number_format($record->total_amount) . ' تومان';
                    })
                    ->sortable(),

                BadgeColumn::make('type')
                    ->label('نوع')
                    ->colors([
                        'primary' => 'payment',
                        'warning' => 'refund',
                        'info' => 'partial_refund',
                    ])
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'payment' => 'پرداخت',
                            'refund' => 'بازگشت وجه',
                            'partial_refund' => 'بازگشت جزئی',
                            default => $state,
                        };
                    }),

                BadgeColumn::make('status')
                    ->label('وضعیت')
                    ->colors([
                        'warning' => 'pending',
                        'info' => 'processing',
                        'success' => 'completed',
                        'danger' => 'failed',
                        'gray' => 'cancelled',
                        'primary' => 'refunded',
                        'secondary' => 'partially_refunded',
                        'dark' => 'expired',
                    ])
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'pending' => 'در انتظار',
                            'processing' => 'در حال پردازش',
                            'completed' => 'تکمیل شده',
                            'failed' => 'ناموفق',
                            'cancelled' => 'لغو شده',
                            'refunded' => 'بازگشت شده',
                            'partially_refunded' => 'بازگشت جزئی',
                            'expired' => 'منقضی شده',
                            default => $state,
                        };
                    }),

                TextColumn::make('gateway_transaction_id')
                    ->label('شناسه درگاه')
                    ->searchable()
                    ->limit(15)
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),

                TextColumn::make('completed_at')
                    ->label('تاریخ تکمیل')
                    ->dateTime('Y/m/d H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('metadata.service_title')
                    ->label('نام سرویس')
                    ->formatStateUsing(function ($state, $record) {
                        $metadata = $record->metadata ?? [];
                        
                        // ALWAYS prioritize service ID for reliable identification
                        // This fixes the sub-service slug duplication issue
                        if (isset($metadata['service_id'])) {
                            $service = \App\Models\Service::find($metadata['service_id']);
                            if ($service) {
                                return $service->title;
                            }
                        }
                        
                        // Fallback to stored title if service not found by ID
                        $serviceTitle = $metadata['service_title'] ?? 
                                      $metadata['service_name'] ?? 
                                      null;
                        
                        if ($serviceTitle) {
                            return $serviceTitle;
                        }
                        
                        // Check if this is a wallet charge transaction type
                        $type = $metadata['type'] ?? $record->type ?? '';
                        if (in_array($type, ['wallet_charge', 'wallet_charge_for_service'])) {
                            return 'شارژ کیف‌پول';
                        }
                        
                        return 'بدون سرویس';
                    })
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('metadata.type')
                    ->label('نوع پرداخت')
                    ->formatStateUsing(fn ($state) => match($state) {
                        'wallet_charge' => 'شارژ کیف‌پول',
                        'service_payment' => 'پرداخت سرویس',
                        'wallet_charge_for_service' => 'شارژ برای سرویس',
                        'guest_wallet_charge' => 'شارژ مهمان',
                        default => $state ?? 'نامشخص'
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'wallet_charge' => 'success',
                        'service_payment' => 'warning',
                        'wallet_charge_for_service' => 'info',
                        'guest_wallet_charge' => 'primary',
                        default => 'gray'
                    }),

                TextColumn::make('metadata.continue_service')
                    ->label('ادامه سرویس')
                    ->formatStateUsing(function ($state, $record) {
                        if (!$state) return 'خیر';
                        
                        if (is_array($state) && isset($state['title'])) {
                            return $state['title'];
                        }
                        
                        return 'بله';
                    })
                    ->badge()
                    ->color(fn ($state) => $state ? 'info' : 'gray')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('user_type')
                    ->label('نوع کاربر')
                    ->formatStateUsing(function ($state, $record) {
                        if ($record->user_id) {
                            return 'کاربر ثبت‌نامی';
                        }
                        
                        return 'کاربر مهمان';
                    })
                    ->badge()
                    ->color(fn ($state, $record) => $record->user_id ? 'success' : 'warning'),

                TextColumn::make('metadata.payment_source')
                    ->label('منبع پرداخت')
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'gateway_payment' => 'درگاه پرداخت',
                            'guest_payment' => 'پرداخت مهمان',
                            'admin_panel' => 'پنل مدیریت',
                            'service_preview' => 'پیش‌نمای سرویس',
                            default => $state ?? 'نامشخص'
                        };
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'gateway_payment' => 'success',
                        'guest_payment' => 'info',
                        'admin_panel' => 'warning',
                        'service_preview' => 'primary',
                        default => 'gray'
                    })
                    ->toggleable(),

                TextColumn::make('guest_conversion_status')
                    ->label('وضعیت تبدیل مهمان')
                    ->state(function ($record) {
                        // Check if this was originally a guest payment that got converted
                        $walletTransactions = \Bavix\Wallet\Models\Transaction::where('meta->gateway_transaction_id', $record->id)
                            ->where('meta->converted_from_guest', true)
                            ->count();
                        
                        if ($walletTransactions > 0) {
                            return 'تبدیل شده از مهمان';
                        }
                        
                        // Check metadata for guest conversion indicators
                        $metadata = $record->metadata ?? [];
                        if (isset($metadata['type']) && $metadata['type'] === 'guest_wallet_charge') {
                            return $record->user_id ? 'تبدیل شده از مهمان' : 'مهمان (در انتظار)';
                        }
                        
                        return $record->user_id ? 'پرداخت کاربر' : 'مهمان';
                    })
                    ->badge()
                    ->color(fn ($state) => match($state) {
                        'تبدیل شده از مهمان' => 'success',
                        'مهمان (در انتظار)' => 'warning',
                        'پرداخت کاربر' => 'primary',
                        'مهمان' => 'gray',
                        default => 'gray'
                    })
                    ->toggleable(),
            ])
            ->filters([
                Filter::make('user_status')
                    ->label('وضعیت کاربر')
                    ->form([
                        Select::make('user_type')
                            ->label('نوع کاربر')
                            ->options([
                                'guest' => 'مهمان',
                                'user' => 'کاربر ثبت‌نامی',
                                'converted' => 'تبدیل شده از مهمان',
                            ])
                            ->placeholder('همه'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (!isset($data['user_type'])) {
                            return $query;
                        }
                        
                        return match($data['user_type']) {
                            'guest' => $query->whereNull('user_id'),
                            'user' => $query->whereNotNull('user_id')
                                ->whereDoesntHave('user.walletTransactions', function ($q) {
                                    $q->where('meta->converted_from_guest', true);
                                }),
                            'converted' => $query->whereNotNull('user_id')
                                ->whereHas('user.walletTransactions', function ($q) use ($query) {
                                    $q->where('meta->converted_from_guest', true)
                                      ->whereColumn('meta->gateway_transaction_id', 'gateway_transactions.id');
                                }),
                            default => $query,
                        };
                    }),

                SelectFilter::make('status')
                    ->label('وضعیت تراکنش')
                    ->options([
                        'pending' => 'در انتظار',
                        'processing' => 'در حال پردازش',
                        'completed' => 'تکمیل شده',
                        'failed' => 'ناموفق',
                        'cancelled' => 'لغو شده',
                        'refunded' => 'بازگردانده شده',
                    ]),

                SelectFilter::make('type')
                    ->label('نوع تراکنش')
                    ->options([
                        'payment' => 'پرداخت',
                        'wallet_charge' => 'شارژ کیف پول',
                        'service_payment' => 'پرداخت سرویس',
                        'guest_wallet_charge' => 'شارژ کیف پول مهمان',
                    ]),

                Filter::make('payment_source')
                    ->label('منبع پرداخت')
                    ->form([
                        Select::make('source')
                            ->label('منبع')
                            ->options([
                                'gateway_payment' => 'درگاه پرداخت',
                                'guest_payment' => 'پرداخت مهمان',
                                'admin_panel' => 'پنل مدیریت',
                                'service_preview' => 'پیش‌نمای سرویس',
                            ])
                            ->placeholder('همه منابع'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        if (isset($data['source'])) {
                            return $query->where('metadata->payment_source', $data['source']);
                        }
                        return $query;
                    }),

                Filter::make('service_payments')
                    ->label('پرداخت‌های مرتبط با سرویس')
                    ->query(function (Builder $query): Builder {
                        return $query->whereNotNull('metadata->service_id');
                    })
                    ->toggle(),

                Filter::make('guest_payments')
                    ->label('پرداخت‌های مهمان')
                    ->query(function (Builder $query): Builder {
                        return $query->whereNull('user_id');
                    })
                    ->toggle(),

                Filter::make('service_continuation')
                    ->label('ادامه سرویس')
                    ->query(function (Builder $query): Builder {
                        return $query->whereNotNull('metadata->continue_service');
                    })
                    ->toggle(),

                Filter::make('created_at')
                    ->label('تاریخ ایجاد')
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

                Filter::make('completed_at')
                    ->label('تاریخ تکمیل')
                    ->form([
                        Forms\Components\DatePicker::make('completed_from')
                            ->label('از تاریخ'),
                        Forms\Components\DatePicker::make('completed_until')
                            ->label('تا تاریخ'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['completed_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('completed_at', '>=', $date),
                            )
                            ->when(
                                $data['completed_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('completed_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Action::make('check_status')
                    ->label('بررسی وضعیت')
                    ->icon('heroicon-o-arrow-path')
                    ->color('info')
                    ->action(function (GatewayTransaction $record) {
                        try {
                            $gatewayManager = app(PaymentGatewayManager::class);
                            $gateway = $gatewayManager->gatewayById($record->payment_gateway_id);
                            $status = $gateway->getPaymentStatus($record);
                            
                            Notification::make()
                                ->title('وضعیت تراکنش')
                                ->body('وضعیت: ' . ($status['status'] ?? 'نامشخص'))
                                ->info()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('خطا در بررسی وضعیت')
                                ->body('خطا: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Action::make('refund')
                    ->label('بازگشت وجه')
                    ->icon('heroicon-o-arrow-uturn-left')
                    ->color('warning')
                    ->visible(fn (GatewayTransaction $record) => $record->status === 'completed')
                    ->action(function (GatewayTransaction $record) {
                        try {
                            $gatewayManager = app(PaymentGatewayManager::class);
                            $gateway = $gatewayManager->gatewayById($record->payment_gateway_id);
                            $result = $gateway->refund($record);
                            
                            if ($result['success']) {
                                Notification::make()
                                    ->title('بازگشت وجه موفق')
                                    ->body('مبلغ با موفقیت بازگشت شد')
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title('خطا در بازگشت وجه')
                                    ->body('خطا: ' . ($result['message'] ?? 'خطای نامشخص'))
                                    ->danger()
                                    ->send();
                            }
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title('خطا در بازگشت وجه')
                                ->body('خطا: ' . $e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),

                Tables\Actions\DeleteAction::make()
                    ->label('حذف'),
            ])
            ->recordUrl(fn (GatewayTransaction $record): string => static::getUrl('edit', ['record' => $record]))
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->label('حذف انتخاب شده‌ها'),
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
            'index' => Pages\ListGatewayTransactions::route('/'),
            'create' => Pages\CreateGatewayTransaction::route('/create'),
            'edit' => Pages\EditGatewayTransaction::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::pending()->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        $count = static::getModel()::pending()->count();
        return $count > 0 ? 'warning' : 'success';
    }

    protected static function exportTransactions($records)
    {
        // Implementation for exporting transactions
        // You can use Laravel Excel or any other export library
        // For now, this is a placeholder
        return response()->streamDownload(function () use ($records) {
            $csv = "UUID,User,Gateway,Amount,Status,Created At\n";
            foreach ($records as $record) {
                $userName = $record->user?->name ?? 'Guest User';
                $csv .= "{$record->uuid},{$userName},{$record->paymentGateway->name},{$record->total_amount},{$record->status},{$record->created_at}\n";
            }
            echo $csv;
        }, 'gateway_transactions.csv');
    }
} 