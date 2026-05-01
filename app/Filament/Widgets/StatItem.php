<?php

namespace App\Filament\Widgets;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\Purchase;
use App\Models\Production;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Carbon\Carbon;

class ItemStatsWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Item', Item::count())
                ->description('Semua jenis item')
                ->descriptionIcon('heroicon-m-square-3-stack-3d')
                ->color('primary'),

            Stat::make('Raw Material', Item::where('type', 'raw_material')->count())
                ->description('Bahan mentah')
                ->descriptionIcon('heroicon-m-fire')
                ->color('warning'),

            Stat::make('Component', Item::where('type', 'component')->count())
                ->description('Bahan olahan')
                ->descriptionIcon('heroicon-m-beaker')
                ->color('info'),

            Stat::make('Finish Good', Item::where('type', 'finish')->count())
                ->description('Produk jadi')
                ->descriptionIcon('heroicon-m-cube')
                ->color('success'),

            Stat::make('Supplier Aktif', Supplier::where('is_active', true)->count())
                ->description('Pemasok terdaftar')
                ->descriptionIcon('heroicon-m-truck')
                ->color('primary'),

            // Stat::make('Nilai Inventory', Item::sum(\DB::raw('current_stock * average_cost')))
            //     ->description('Total nilai stok (IDR)')
            //     ->descriptionIcon('heroicon-m-banknotes')
            //     ->money('IDR')
            //     ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
            //     ->color('success'),
        ];
    }
}
