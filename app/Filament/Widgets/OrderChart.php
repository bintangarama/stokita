<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrderChart extends ChartWidget
{
    protected ?string $heading = 'Pesanan per Bulan';
    protected static ?int $sort = 2;
    protected function getData(): array
    {
        $year = now()->year;

        $orders = Order::query()
            ->selectRaw('MONTH(order_date) as month, COUNT(*) as total')
            ->whereYear('order_date', $year)
            ->whereIn('status', ['confirmed', 'completed'])
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        // Pastikan 12 bulan selalu ada (biar chart rapi)
        $data = [];
        $labels = [];

        for ($month = 1; $month <= 12; $month++) {
            $labels[] = Carbon::create()->month($month)->translatedFormat('M');
            $data[] = $orders[$month] ?? 0;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Pesanan',
                    'data' => $data,
                    'tension' => 0.4, // garis lebih smooth
                    'borderWidth' => 2,
                    'pointRadius' => 4,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
