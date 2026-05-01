<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Services\SalesService;
use Filament\Resources\Pages\CreateRecord;

class CreateOrder extends CreateRecord
{
    protected static string $resource = OrderResource::class;

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // generate nomor otomatis sebelum form tampil (hanya saat create)
        $last = Order::orderBy('id', 'desc')->first();
        $next = $last ? ((int) substr($last->order_no, 4)) + 1 : 1;

        $data['order_no'] = 'ORD-' . str_pad($next, 4, '0', STR_PAD_LEFT);

        return $data;
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        return $data;
    }
    protected function afterCreate(): void
    {
        SalesService::process($this->record);
    }
}
