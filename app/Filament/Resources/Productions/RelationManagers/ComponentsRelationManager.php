<?php

namespace App\Filament\Resources\Productions\RelationManagers;

use App\Filament\Resources\Productions\ProductionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ComponentsRelationManager extends RelationManager
{
    protected static string $relationship = 'components';

    protected static ?string $relatedResource = ProductionResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ingredientItem.name')
                    ->label('Bahan')
                    ->sortable(),

                TextColumn::make('unit.name')
                    ->label('Satuan'),

                TextColumn::make('qty_used')
                    ->label('Qty Digunakan')
                    ->numeric(4),

                TextColumn::make('unit_cost')
                    ->label('Biaya per Unit')
                    ->money('IDR', true),

                TextColumn::make('line_total_cost')
                    ->label('Total Biaya')
                    ->money('IDR', true),
            ])
            ->paginated(false)
            ->defaultSort('id')
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
