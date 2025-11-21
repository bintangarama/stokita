<?php

namespace App\Filament\Widgets;

use App\Models\Purchase;
use Filament\Widgets\ChartWidget;

class PurchaseChartWidget extends ChartWidget
{
    // protected static ?string $heading = 'Pembelian 30 Hari Terakhir';

    protected function getData(): array
    {
        $labels = [];
        $values = [];

        foreach (range(29, 0) as $i) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels[] = $date;

            $values[] = Purchase::whereDate('purchase_date', $date)->sum('total_amount');
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Pembelian',
                    'data' => $values,
                    'borderColor' => '#3B82F6',
                ]
            ]
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
