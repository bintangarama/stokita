<?php

namespace App\Filament\Resources\Purchases\RelationManagers;

use App\Filament\Resources\Purchases\PurchaseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $relatedResource = PurchaseResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('item.name')
            ->columns([
                TextColumn::make('item.name')
                    ->label('Item')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('unit.name')
                    ->label('Satuan'),

                TextColumn::make('qty')
                    ->label('Qty')
                    ->numeric(4),

                TextColumn::make('unit_price')
                    ->label('Harga Satuan')
                    ->money('IDR', true),

                TextColumn::make('line_total')
                    ->label('Subtotal')
                    ->money('IDR', true),
            ])
            ->actions([]) // hilangkan tombol edit/delete
            ->paginated(false)
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
