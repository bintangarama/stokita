<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class CustomerChart extends ChartWidget
{
    protected ?string $heading = 'Total Pelanggan';
    protected static ?int $sort = 3;
    protected function getData(): array
    {
        $year = now()->year;

        // Ambil jumlah customer baru per bulan
        $customersPerMonth = Customer::query()
            ->selectRaw('MONTH(created_at) as month, COUNT(*) as total')
            ->where('is_active', true)
            ->whereYear('created_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $labels = [];
        $data = [];
        $runningTotal = 0;

        // Buat data 12 bulan (biar grafik konsisten)
        for ($month = 1; $month <= 12; $month++) {
            $labels[] = Carbon::create()->month($month)->translatedFormat('M');
            $runningTotal += $customersPerMonth[$month] ?? 0;
            $data[] = $runningTotal;
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Pelanggan',
                    'data' => $data,
                    'tension' => 0.4,
                    'fill' => true,
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
