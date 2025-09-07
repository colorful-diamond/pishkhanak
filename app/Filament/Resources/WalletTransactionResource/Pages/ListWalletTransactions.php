<?php

namespace App\Filament\Resources\WalletTransactionResource\Pages;

use App\Filament\Resources\WalletTransactionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListWalletTransactions extends ListRecords
{
    protected static string $resource = WalletTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('همه تراکنش‌ها'),
            'deposits' => Tab::make('واریزی‌ها')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'deposit')),
            'withdrawals' => Tab::make('برداشت‌ها')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('type', 'withdraw')),
            'pending' => Tab::make('در انتظار تایید')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('confirmed', false)),
        ];
    }
} 