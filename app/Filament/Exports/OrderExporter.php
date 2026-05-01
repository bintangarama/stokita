<?php

namespace App\Filament\Exports;

use App\Models\Order;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class OrderExporter extends Exporter
{
    protected static ?string $model = Order::class;

    public static function getColumns(): array
    {
        return [
            // ExportColumn::make('id')
            //     ->label('ID'),
            ExportColumn::make('order_no')
                ->label('No Pesanan'),
            ExportColumn::make('order_date')
                ->label('Tanggal Pesanan')
                ->formatStateUsing(
                    fn($state) =>
                    $state?->format('d-m-Y')
                ),
            ExportColumn::make('customer.name')
                ->label('Pelanggan'),
            ExportColumn::make('total_amount')
                ->label('Total'),
            // ->money('IDR'),
            ExportColumn::make('discount')
                ->label('Diskon'),
            // ->money('IDR')
            ExportColumn::make('grand_total')
                ->label('Grand Total'),
            // ->money('IDR'),
            ExportColumn::make('status')
                ->label('Status')
                ->formatStateUsing(fn(string $state) => match ($state) {
                    'draft' => 'Draft',
                    'confirmed' => 'Dikonfirmasi',
                    'completed' => 'Selesai',
                    'cancelled' => 'Dibatalkan',
                    default => $state,
                }),
            ExportColumn::make('created_by'),
            ExportColumn::make('notes'),
            ExportColumn::make('created_at'),
            ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your order export has completed and ' . Number::format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
