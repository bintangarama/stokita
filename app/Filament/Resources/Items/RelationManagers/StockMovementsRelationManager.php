<?php

namespace App\Filament\Resources\Items\RelationManagers;

use App\Filament\Resources\Items\ItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class StockMovementsRelationManager extends RelationManager
{
    protected static string $relationship = 'stockMovements';

    protected static ?string $relatedResource = ItemResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('movement_type')
                    ->label('Jenis')
                    ->badge()
                    ->colors([
                        'success' => 'purchase_in',
                        'danger'  => ['production_out', 'sale_out', 'adjustment'],
                        'primary' => ['production_in', 'transfer_in'],
                        'warning' => ['transfer_out'],
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'purchase_in'    => 'Pembelian Masuk',
                        'production_out' => 'Pemakaian Produksi',
                        'production_in'  => 'Hasil Produksi',
                        'sale_out'       => 'Penjualan Keluar',
                        'adjustment'     => 'Penyesuaian',
                        'transfer_in'    => 'Transfer Masuk',
                        'transfer_out'   => 'Transfer Keluar',
                        default          => $state,
                    }),

                TextColumn::make('qty')
                    ->label('Qty')
                    ->numeric(4),

                TextColumn::make('unit.name')
                    ->label('Satuan'),

                TextColumn::make('unit_cost')
                    ->label('Cost')
                    ->money('IDR', true),

                TextColumn::make('reference_table')
                    ->label('Referensi')
                    ->formatStateUsing(fn($state) => ucfirst($state ?? '-')),

                TextColumn::make('reference_id')
                    ->label('ID Ref.'),

                TextColumn::make('created_by')
                    ->label('User')
                    ->formatStateUsing(
                        fn($state) =>
                        optional(\App\Models\User::find($state))->name ?? '-'
                    ),

                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime(),
            ])

            ->defaultSort('created_at', 'desc')
            ->filters([
                // 
            ])
            ->recordActions([
                // 
            ])
            ->toolbarActions([
                //
            ])
            ->headerActions([
                // CreateAction::make(),
            ]);
    }
}
