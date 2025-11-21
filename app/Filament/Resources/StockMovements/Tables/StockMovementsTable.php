<?php

namespace App\Filament\Resources\StockMovements\Tables;

use App\Models\Item;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter as FiltersFilter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use SebastianBergmann\CodeCoverage\Filter;

class StockMovementsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('item.name')
                    ->label('Item')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('movement_type')
                    ->label('Tipe')
                    ->colors([
                        'success' => 'purchase_in',
                        'danger'  => ['production_out', 'sale_out', 'adjustment'],
                        'primary' => ['production_in', 'transfer_in'],
                        'warning' => ['transfer_out'],
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'purchase_in'   => 'Pembelian Masuk',
                        'production_out' => 'Pemakaian Produksi',
                        'production_in' => 'Hasil Produksi',
                        'sale_out'      => 'Penjualan Keluar',
                        'adjustment'    => 'Penyesuaian Stok',
                        'transfer_in'   => 'Transfer Masuk',
                        'transfer_out'  => 'Transfer Keluar',
                        default         => $state,
                    }),
                TextColumn::make('reference_table')
                    ->label('Referensi')
                    ->formatStateUsing(fn($state) => ucfirst($state ?? '-'))
                    ->sortable(),
                TextColumn::make('reference_id')
                    ->label('ID Ref.')
                    ->sortable(),
                TextColumn::make('qty')
                    ->label('Qty')
                    ->numeric(4),
                TextColumn::make('unit.name')
                    ->label('Satuan'),
                TextColumn::make('unit_cost')
                    ->label('Cost')
                    ->money('IDR', true),
                TextColumn::make('created_by')
                    ->label('User')
                    ->formatStateUsing(fn($state) => optional(\App\Models\User::find($state))->name ?? '-'),
                TextColumn::make('created_at')
                    ->label('Tanggal')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                //
                SelectFilter::make('item_id')
                    ->label('Filter Item')
                    ->options(Item::pluck('name', 'id'))
                    ->searchable(),

                SelectFilter::make('movement_type')
                    ->label('Jenis Movement')
                    ->options([
                        'purchase_in'   => 'Pembelian Masuk',
                        'production_out' => 'Pemakaian Produksi',
                        'production_in' => 'Hasil Produksi',
                        'sale_out'      => 'Penjualan Keluar',
                        'adjustment'    => 'Penyesuaian',
                        'transfer_in'   => 'Transfer Masuk',
                        'transfer_out'  => 'Transfer Keluar',
                    ]),
                FiltersFilter::make('created_at')
                    ->schema([
                        DatePicker::make('from'),
                        DatePicker::make('until'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
                            ->when($data['until'] ?? null, fn($q, $date) => $q->whereDate('created_at', '<=', $date));
                    })
                    ->label('Tanggal'),
            ])
            ->recordActions([
                ViewAction::make(),
                // EditAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
