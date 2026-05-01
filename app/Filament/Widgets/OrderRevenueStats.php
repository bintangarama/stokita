<?php

namespace App\Filament\Widgets;

use App\Models\Customer;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class OrderRevenueStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $now = Carbon::now();
        $lastMonth = $now->copy()->subMonth();

        $validOrderStatus = ['confirmed', 'completed'];

        /*
        |--------------------------------------------------------------------------
        | REVENUE
        |--------------------------------------------------------------------------
        */
        $revenueThisMonth = Order::query()
            ->whereIn('status', $validOrderStatus)
            ->whereMonth('order_date', $now->month)
            ->whereYear('order_date', $now->year)
            ->sum('grand_total');

        $revenueLastMonth = Order::query()
            ->whereIn('status', $validOrderStatus)
            ->whereMonth('order_date', $lastMonth->month)
            ->whereYear('order_date', $lastMonth->year)
            ->sum('grand_total');

        $revenueDiff = $revenueThisMonth - $revenueLastMonth;

        /*
        |--------------------------------------------------------------------------
        | NEW ORDERS
        |--------------------------------------------------------------------------
        */
        $ordersThisMonth = Order::query()
            ->whereIn('status', $validOrderStatus)
            ->whereMonth('order_date', $now->month)
            ->whereYear('order_date', $now->year)
            ->count();

        $ordersLastMonth = Order::query()
            ->whereIn('status', $validOrderStatus)
            ->whereMonth('order_date', $lastMonth->month)
            ->whereYear('order_date', $lastMonth->year)
            ->count();

        $ordersDiff = $ordersThisMonth - $ordersLastMonth;

        /*
        |--------------------------------------------------------------------------
        | NEW CUSTOMERS
        |--------------------------------------------------------------------------
        */
        $customersThisMonth = Customer::query()
            ->where('is_active', true)
            ->whereMonth('created_at', $now->month)
            ->whereYear('created_at', $now->year)
            ->count();

        $customersLastMonth = Customer::query()
            ->where('is_active', true)
            ->whereMonth('created_at', $lastMonth->month)
            ->whereYear('created_at', $lastMonth->year)
            ->count();

        $customersDiff = $customersThisMonth - $customersLastMonth;

        return [
            Stat::make(
                'Pendapatan Bulan Ini',
                'Rp ' . number_format($revenueThisMonth, 0, ',', '.')
            )
                ->description($this->formatDiffCurrency($revenueDiff))
                ->descriptionIcon(
                    $revenueDiff >= 0
                        ? 'heroicon-m-arrow-trending-up'
                        : 'heroicon-m-arrow-trending-down'
                )
                ->color($revenueDiff >= 0 ? 'success' : 'danger'),

            Stat::make(
                'Pesanan Baru',
                $ordersThisMonth
            )
                ->description($this->formatDiffNumber($ordersDiff))
                ->descriptionIcon(
                    $ordersDiff >= 0
                        ? 'heroicon-m-arrow-trending-up'
                        : 'heroicon-m-arrow-trending-down'
                )
                ->color($ordersDiff >= 0 ? 'success' : 'danger'),

            Stat::make(
                'Pelanggan Baru',
                $customersThisMonth
            )
                ->description($this->formatDiffNumber($customersDiff))
                ->descriptionIcon(
                    $customersDiff >= 0
                        ? 'heroicon-m-arrow-trending-up'
                        : 'heroicon-m-arrow-trending-down'
                )
                ->color($customersDiff >= 0 ? 'success' : 'danger'),
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Helper Formatter
    |--------------------------------------------------------------------------
    */
    private function formatDiffCurrency(float $value): string
    {
        if ($value === 0.0) {
            return 'Tidak ada perubahan';
        }

        $prefix = $value > 0 ? '+' : '-';

        return $prefix . 'Rp ' . number_format(abs($value), 0, ',', '.') . ' dibanding bulan lalu';
    }

    private function formatDiffNumber(int $value): string
    {
        if ($value === 0) {
            return 'Tidak ada perubahan';
        }

        $prefix = $value > 0 ? '+' : '-';

        return $prefix . abs($value) . ' dibanding bulan lalu';
    }
}
