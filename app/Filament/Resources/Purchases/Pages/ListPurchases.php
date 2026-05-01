<?php

namespace App\Filament\Resources\Purchases\Pages;

use App\Filament\Exports\PurchaseExporter;
use App\Filament\Resources\Purchases\PurchaseResource;
use Filament\Actions\CreateAction;
use Filament\Actions\ExportAction;
use Filament\Resources\Pages\ListRecords;

class ListPurchases extends ListRecords
{
    protected static string $resource = PurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExportAction::make('export')
                ->label('Export Data')
                ->fileName(fn() => 'pesanan-' . now()->format('Y-m-d'))
                ->exporter(PurchaseExporter::class),
            CreateAction::make(),
        ];
    }
}
