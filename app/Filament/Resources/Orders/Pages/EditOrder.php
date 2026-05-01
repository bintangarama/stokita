<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use App\Services\SalesService;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Components\Section;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    // protected function mutateFormDataBeforeSave(array $data): array
    // {
    //     $data['created_by'] = auth()->id(); // optional, bisa juga tidak berubah
    //     return $data;
    // }
    protected function afterSave(): void
    {
        SalesService::process($this->record);
    }

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($this->record->isCompleted()) {
            throw new \Exception("Order completed tidak dapat diubah.");
        }

        $data['created_by'] = auth()->id();

        return $data;
    }
}
