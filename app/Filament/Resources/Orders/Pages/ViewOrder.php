<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Services\SalesService;
use App\Services\UnitConversionService;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            // CONFIRM
            Action::make('confirm')
                ->label('Confirm Order')
                ->color('warning')
                ->icon('heroicon-o-check')
                ->visible(fn() => $this->record->status === 'draft')
                ->requiresConfirmation()
                ->action(function () {
                    foreach ($this->record->items as $orderItem) {

                        $item = $orderItem->item;

                        $qtyBase = UnitConversionService::convert(
                            $orderItem->qty,
                            $orderItem->unit_id,
                            $item->base_unit_id
                        );

                        if ($qtyBase > $item->current_stock) {
                            Notification::make()
                                ->title("Stok tidak cukup!")
                                ->body("{$item->name} hanya tersedia {$item->current_stock}. Diminta {$qtyBase}.")
                                ->danger()
                                ->send();

                            $this->halt();
                        }
                    }

                    // Jika lolos semua validasi → proses order
                    $this->record->update(['status' => 'confirmed']);
                    SalesService::process($this->record);

                    Notification::make()
                        ->title("Order dikonfirmasi.")
                        ->success()
                        ->send();

                    $this->refreshRecord();
                }),

            // COMPLETE
            Action::make('complete')
                ->label('Complete Order')
                ->color('success')
                ->icon('heroicon-o-flag')
                ->visible(fn() => $this->record->status === 'confirmed')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => 'completed']);
                    SalesService::process($this->record);
                    // $this->notify('success', 'Order telah diselesaikan.');
                    Notification::make()
                        ->title('Order berhasil diselesaikan.')
                        ->success()
                        ->send();
                    $this->refreshRecord();
                }),

            // CANCEL
            Action::make('cancel')
                ->label('Cancel Order')
                ->color('danger')
                ->icon('heroicon-o-x-circle')
                ->visible(fn() => in_array($this->record->status, ['draft', 'confirmed']))
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->update(['status' => 'cancelled']);
                    SalesService::process($this->record);
                    Notification::make()
                        ->title('Order berhasil diselesaikan.')
                        ->success()
                        ->send();
                    $this->refreshRecord();
                    // $this->notify('danger', 'Order telah dibatalkan & stok dipulihkan.');
                }),
        ];
    }
    /**
     * Refresh data after any action
     */
    protected function refreshRecord(): void
    {
        $this->record->refresh();
        $this->dispatch('refresh');
    }
}
