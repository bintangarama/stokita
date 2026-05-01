<?php

namespace App\Filament\Resources\Orders\RelationManagers;

use App\Models\Item;
use App\Services\UnitConversionService;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Notifications\Notification;

class OrderItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    // Only show on ViewOrder page
    public static function canViewForRecord($ownerRecord, $pageClass): bool
    {
        return str_contains($pageClass, 'ViewOrder');
    }

    // Disable on EditOrder or CreateOrder
    public static function canEditForRecord($ownerRecord, $pageClass): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        $parent = $this->ownerRecord;

        return $table
            ->columns([
                TextColumn::make('item.name')->label('Item'),
                TextColumn::make('qty')->label('Qty'),
                TextColumn::make('unit.name')->label('Unit'),
                TextColumn::make('unit_price')->money('IDR'),
                TextColumn::make('line_total')->money('IDR'),
            ])

            // ============================
            // HEADER ACTIONS
            // ============================
            ->headerActions([
                CreateAction::make()
                    ->visible(fn() => $parent->status === 'draft')
                    ->before(function (array $data) {
                        $item = Item::find($data['item_id']);

                        if (!$item) {
                            throw new \Exception("Item tidak ditemukan.");
                        }

                        // konversi qty ke base unit
                        $qtyBase = UnitConversionService::convert(
                            $data['qty'],
                            $data['unit_id'],
                            $item->base_unit_id
                        );

                        if ($qtyBase > $item->current_stock) {
                            Notification::make()
                                ->title("Stok tidak cukup untuk {$item->name}")
                                ->body("Stok tersedia: {$item->current_stock}, diminta: {$qtyBase}")
                                ->danger()
                                ->send();

                            // menghentikan aksi create
                            $this->halt();
                        }
                    })
                    ->after(function () use ($parent) {
                        // Recalculate totals ONLY (not stock)
                        $parent->update([
                            'grand_total' => $parent->items()->sum('line_total') - $parent->discount,
                            'total_amount' => $parent->items()->sum('line_total'),
                        ]);
                    }),
            ])

            // ============================
            // RECORD ACTIONS
            // ============================
            ->recordActions([
                EditAction::make()
                    ->visible(fn() => $parent->status === 'draft')
                    ->before(function (array $data, $record) {
                        $item = \App\Models\Item::find($data['item_id']);

                        $qtyBase = UnitConversionService::convert(
                            $data['qty'],
                            $data['unit_id'],
                            $item->base_unit_id
                        );

                        // Hitung stok yang tersisa jika item lama dihapus dulu
                        $oldQtyBase = UnitConversionService::convert(
                            $record->qty,
                            $record->unit_id,
                            $item->base_unit_id
                        );

                        $availableStock = $item->current_stock + $oldQtyBase;

                        if ($qtyBase > $availableStock) {
                            Notification::make()
                                ->title("Stok tidak cukup untuk {$item->name}")
                                ->body("Stok tersedia: {$availableStock}, diminta: {$qtyBase}")
                                ->danger()
                                ->send();

                            $this->halt();
                        }
                    })
                    ->after(function ($record) use ($parent) {
                        $record->update([
                            'line_total' => $record->qty * $record->unit_price,
                        ]);

                        $parent->update([
                            'grand_total' => $parent->items()->sum('line_total') - $parent->discount,
                            'total_amount' => $parent->items()->sum('line_total'),
                        ]);
                    }),

                DeleteAction::make()
                    ->visible(fn() => $parent->status === 'draft')
                    ->after(function ($record) use ($parent) {
                        $record->delete();

                        $parent->update([
                            'grand_total' => $parent->items()->sum('line_total') - $parent->discount,
                            'total_amount' => $parent->items()->sum('line_total'),
                        ]);
                    }),
            ]);
    }
}
