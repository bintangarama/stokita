<?php

namespace App\Filament\Resources\Orders\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class OrderStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Orders', Order::count())
                ->description('Total pesanan')
                ->descriptionIcon('heroicon-o-shopping-bag')
                ->color('primary'),

            Stat::make('Open Orders', Order::whereIn('status', ['draft', 'confirmed'])->count())
                ->description('Belum selesai')
                ->descriptionIcon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Average Order', number_format((float) Order::avg('grand_total'), 2))
                ->description('Rata-rata nilai pesanan')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('success'),
        ];
    }
}
