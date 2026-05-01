<?php

namespace App\Filament\Resources\Productions\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ProductionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('producedItem.name')
                    ->label('Item Hasil')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('recipe.name')
                    ->label('Resep')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('produced_qty')
                    ->label('Jumlah')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('unit.name')
                    ->label('Satuan')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('cost_total')
                    ->label('Total Biaya')
                    ->money('IDR', true),
                TextColumn::make('overhead_cost')
                    ->label('Overhead')
                    ->money('IDR', true),
                TextColumn::make('selling_price')
                    ->label('Harga Jual')
                    ->money('IDR', true)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('produced_by')
                    ->label('Dibuat Oleh')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('produced_at')
                    ->label('Tanggal Produksi')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
