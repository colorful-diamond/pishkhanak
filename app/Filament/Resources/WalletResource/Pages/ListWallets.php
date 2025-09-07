<?php

namespace App\Filament\Resources\WalletResource\Pages;

use App\Filament\Resources\WalletResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListWallets extends ListRecords
{
    protected static string $resource = WalletResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('همه کیف‌پول‌ها'),
            'active' => Tab::make('کیف‌پول‌های فعال')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('balance', '>', 0)),
            'empty' => Tab::make('کیف‌پول‌های خالی')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('balance', '=', 0)),
        ];
    }
} 