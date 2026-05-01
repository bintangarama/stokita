<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Exports\OrderExporter;
use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Orders\Widgets\OrderStatsOverview;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make('export')
                ->label('Export Data')
                ->fileName(fn() => 'order-' . now()->format('Y-m-d'))
                ->exporter(OrderExporter::class),
            // Optional: batasi format yang diizinkan
            // ->formats([
            //     'xlsx',
            //     'csv',
            // ]),
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            OrderStatsOverview::class,
        ];
    }

    /**
     * Tambahkan Tabs seperti demo Filament shop
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),

            'draft' => Tab::make('Draft')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'draft')),

            'confirmed' => Tab::make('Confirmed')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'confirmed')),

            'completed' => Tab::make('Completed')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'completed')),

            'cancelled' => Tab::make('Cancelled')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'cancelled')),
        ];
    }
}
