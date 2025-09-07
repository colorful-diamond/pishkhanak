<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentSourceResource\Pages;
use App\Models\Service;
use App\Models\ServiceResult;
use App\Models\GatewayTransaction;
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
use Illuminate\Support\Facades\DB;

class PaymentSourceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationLabel = 'منابع پرداخت';

    protected static ?string $modelLabel = 'منبع پرداخت';

    protected static ?string $pluralModelLabel = 'منابع پرداخت';

    protected static ?string $navigationGroup = 'مدیریت مالی';

    protected static ?int $navigationSort = 10;

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Service::query()
                    ->select([
                        'services.*',
                        DB::raw('COUNT(DISTINCT service_results.id) as results_count'),
                        DB::raw('COUNT(DISTINCT gateway_transactions.id) as gateway_payments_count'),
                        DB::raw('COUNT(DISTINCT wallet_transactions.id) as wallet_payments_count'),
                        DB::raw('COALESCE(SUM(DISTINCT gateway_transactions.amount), 0) as total_gateway_revenue'),
                        DB::raw('COALESCE(SUM(DISTINCT wallet_transactions.amount), 0) as total_wallet_revenue'),
                        DB::raw('COALESCE(SUM(DISTINCT gateway_transactions.amount), 0) + COALESCE(SUM(DISTINCT wallet_transactions.amount), 0) as total_revenue')
                    ])
                    ->leftJoin('service_results', 'services.id', '=', 'service_results.service_id')
                    ->leftJoin('gateway_transactions', function($join) {
                        $join->on(DB::raw("(gateway_transactions.metadata->>'service_id')::text"), '=', DB::raw('services.id::text'))
                             ->where('gateway_transactions.status', 'completed');
                    })
                    ->leftJoin('transactions as wallet_transactions', function($join) {
                        $join->on(DB::raw("(wallet_transactions.meta->>'service_id')::text"), '=', DB::raw('services.id::text'))
                             ->where('wallet_transactions.confirmed', true);
                    })
                    ->groupBy('services.id')
                    ->having('results_count', '>', 0)
            )
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->label('نام سرویس')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('price')
                    ->label('قیمت سرویس')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' تومان')
                    ->sortable(),

                Tables\Columns\TextColumn::make('results_count')
                    ->label('تعداد استفاده')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                Tables\Columns\TextColumn::make('gateway_payments_count')
                    ->label('پرداخت درگاه')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('wallet_payments_count')
                    ->label('پرداخت کیف‌پول')
                    ->badge()
                    ->color('warning')
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_gateway_revenue')
                    ->label('درآمد درگاه')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' تومان')
                    ->sortable()
                    ->color('success'),

                Tables\Columns\TextColumn::make('total_wallet_revenue')
                    ->label('درآمد کیف‌پول')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' تومان')
                    ->sortable()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('total_revenue')
                    ->label('کل درآمد')
                    ->formatStateUsing(fn ($state) => number_format($state) . ' تومان')
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('تاریخ ایجاد')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('revenue_range')
                    ->label('محدوده درآمد')
                    ->form([
                        Forms\Components\TextInput::make('revenue_from')
                            ->label('از مبلغ')
                            ->numeric(),
                        Forms\Components\TextInput::make('revenue_to')
                            ->label('تا مبلغ')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['revenue_from'],
                                fn (Builder $query, $value): Builder => $query->having('total_revenue', '>=', $value),
                            )
                            ->when(
                                $data['revenue_to'],
                                fn (Builder $query, $value): Builder => $query->having('total_revenue', '<=', $value),
                            );
                    }),

                Tables\Filters\Filter::make('usage_range')
                    ->label('محدوده استفاده')
                    ->form([
                        Forms\Components\TextInput::make('usage_from')
                            ->label('از تعداد')
                            ->numeric(),
                        Forms\Components\TextInput::make('usage_to')
                            ->label('تا تعداد')
                            ->numeric(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['usage_from'],
                                fn (Builder $query, $value): Builder => $query->having('results_count', '>=', $value),
                            )
                            ->when(
                                $data['usage_to'],
                                fn (Builder $query, $value): Builder => $query->having('results_count', '<=', $value),
                            );
                    }),

                Tables\Filters\SelectFilter::make('category')
                    ->label('دسته‌بندی')
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_details')
                    ->label('جزئیات پرداخت‌ها')
                    ->icon('heroicon-o-eye')
                    ->url(fn (Service $record): string => route('filament.access.resources.payment-source-details.index', [
                        'service_id' => $record->id
                    ]))
                    ->openUrlInNewTab(),

                Tables\Actions\ViewAction::make(),
            ])
            ->defaultSort('total_revenue', 'desc')
            ->poll('30s'); // Auto-refresh every 30 seconds
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('اطلاعات سرویس')
                    ->schema([
                        Infolists\Components\TextEntry::make('title')
                            ->label('نام سرویس'),

                        Infolists\Components\TextEntry::make('price')
                            ->label('قیمت')
                            ->formatStateUsing(fn ($state) => number_format($state) . ' تومان'),

                        Infolists\Components\TextEntry::make('category.name')
                            ->label('دسته‌بندی'),

                        Infolists\Components\TextEntry::make('is_paid')
                            ->label('نوع سرویس')
                            ->formatStateUsing(fn ($state) => $state ? 'پولی' : 'رایگان')
                            ->badge()
                            ->color(fn ($state) => $state ? 'warning' : 'success'),
                    ])->columns(2),

                Infolists\Components\Section::make('آمار استفاده')
                    ->schema([
                        Infolists\Components\TextEntry::make('results_count')
                            ->label('تعداد کل استفاده')
                            ->state(function (Service $record) {
                                return ServiceResult::where('service_id', $record->id)->count();
                            }),

                        Infolists\Components\TextEntry::make('successful_results')
                            ->label('استفاده موفق')
                            ->state(function (Service $record) {
                                return ServiceResult::where('service_id', $record->id)
                                    ->where('status', 'success')->count();
                            }),

                        Infolists\Components\TextEntry::make('this_month_usage')
                            ->label('استفاده این ماه')
                            ->state(function (Service $record) {
                                return ServiceResult::where('service_id', $record->id)
                                    ->whereMonth('created_at', now()->month)
                                    ->whereYear('created_at', now()->year)
                                    ->count();
                            }),

                        Infolists\Components\TextEntry::make('today_usage')
                            ->label('استفاده امروز')
                            ->state(function (Service $record) {
                                return ServiceResult::where('service_id', $record->id)
                                    ->whereDate('created_at', today())
                                    ->count();
                            }),
                    ])->columns(2),

                Infolists\Components\Section::make('آمار درآمد')
                    ->schema([
                        Infolists\Components\TextEntry::make('total_gateway_revenue')
                            ->label('درآمد از درگاه پرداخت')
                            ->state(function (Service $record) {
                                return number_format(
                                    GatewayTransaction::where('status', 'completed')
                                        ->whereRaw("metadata->>'service_id' = ?", [(string)$record->id])
                                        ->sum('amount')
                                ) . ' تومان';
                            }),

                        Infolists\Components\TextEntry::make('total_wallet_revenue')
                            ->label('درآمد از کیف‌پول')
                            ->state(function (Service $record) {
                                return number_format(
                                    Transaction::where('confirmed', true)
                                        ->whereRaw("meta->>'service_id' = ?", [(string)$record->id])
                                        ->where('type', 'withdraw')
                                        ->sum('amount')
                                ) . ' تومان';
                            }),

                        Infolists\Components\TextEntry::make('estimated_profit')
                            ->label('تخمین سود')
                            ->state(function (Service $record) {
                                $totalUsage = ServiceResult::where('service_id', $record->id)
                                    ->where('status', 'success')->count();
                                $estimatedRevenue = $totalUsage * $record->price;
                                $estimatedCost = $totalUsage * ($record->cost ?? 0);
                                return number_format($estimatedRevenue - $estimatedCost) . ' تومان';
                            }),

                        Infolists\Components\TextEntry::make('average_monthly_revenue')
                            ->label('میانگین درآمد ماهانه')
                            ->state(function (Service $record) {
                                $monthsActive = $record->created_at->diffInMonths(now()) + 1;
                                $totalUsage = ServiceResult::where('service_id', $record->id)
                                    ->where('status', 'success')->count();
                                $avgMonthlyUsage = $totalUsage / $monthsActive;
                                return number_format($avgMonthlyUsage * $record->price) . ' تومان';
                            }),
                    ])->columns(2),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentSources::route('/'),
            'view' => Pages\ViewPaymentSource::route('/{record}'),
        ];
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'short_title'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        $usageCount = ServiceResult::where('service_id', $record->id)->count();
        $totalRevenue = $usageCount * $record->price;
        
        return [
            'قیمت' => number_format($record->price) . ' تومان',
            'استفاده' => $usageCount . ' بار',
            'درآمد' => number_format($totalRevenue) . ' تومان',
        ];
    }

    public static function canCreate(): bool
    {
        return false; // This is an analytics resource, no creation needed
    }

    public static function canEdit(Model $record): bool
    {
        return false; // This is an analytics resource, no editing needed
    }

    public static function canDelete(Model $record): bool
    {
        return false; // This is an analytics resource, no deletion needed
    }
} 