<?php

namespace App\Filament\Resources\Items\Widgets;

use App\Models\Item;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ItemStats extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $count = Item::whereColumn('current_stock', '<=', 'reorder_threshold')->count();
        return [
            //
            Stat::make('Total Produk', Item::count())
                ->icon('heroicon-o-cube')
                ->description('Jumlah seluruh item'),
            Stat::make('Stok Menipis', $count)
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning')
                ->description('Perlu segera restock'),
            Stat::make('Stok Habis', Item::where('current_stock', '<=', 0)->count())
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->description('Tidak bisa diproduksi'),
        ];
    }
}
