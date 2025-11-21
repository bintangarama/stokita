<?php

namespace App\Filament\Resources\Items\Tables;

use App\Models\Item;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
// use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class ItemsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sku')
                    ->label('SKU')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('name')
                    ->label('Nama Item')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('type')
                    ->label('Jenis/Tipe')
                    ->badge()
                    ->colors([
                        'warning' => 'raw_material',
                        'info' => 'component',
                        'success' => 'finish_good',
                    ])
                    ->formatStateUsing(fn(string $state) => match ($state) {
                        'raw_material' => 'Raw Material',
                        'component' => 'Component',
                        'finish_good' => 'Finish Good',
                        default => $state,
                    }),
                TextColumn::make('baseUnit.name')
                    ->label('Satuan Dasar')
                    ->sortable(),
                IconColumn::make('track_stock')
                    ->boolean(),
                TextColumn::make('reorder_threshold')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('current_stock')
                    ->label('Stok Sekarang')
                    ->numeric(4)
                    ->sortable()
                    ->color(
                        fn(Item $record) =>
                        $record->track_stock && $record->current_stock <= $record->reorder_threshold
                            ? 'danger'
                            : 'success'
                    )
                    ->formatStateUsing(fn($state) => number_format($state, 4)),
                TextColumn::make('average_cost')
                    ->label('Harga Rata-rata')
                    ->money('IDR', true)
                    // ->numeric()
                    ->sortable(),
                TextColumn::make('last_purchase_price')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('selling_price')
                    ->label('Harga Jual')
                    ->money('IDR', true)
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
