<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\Items\ItemResource;
use App\Models\Item as ModelsItem;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Item;

class LowStockItems extends TableWidget
{
    protected static ?string $heading = 'Stok Menipis';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(fn(): Builder => ModelsItem::query()
                ->where('track_stock', true)
                ->whereColumn('current_stock', '<=', 'reorder_threshold')
                ->orderBy('current_stock')
                ->limit(10))
            ->columns([
                //
                TextColumn::make('name')
                    ->label('Item')
                    ->searchable()
                    ->weight('bold'),

                TextColumn::make('current_stock')
                    ->label('Stok Saat Ini  ')
                    ->color(
                        fn($state, $record) =>
                        $state <= 0 ? 'danger' : 'warning'
                    )
                    ->suffix(fn($record) => ' ' . $record->baseUnit?->name),

                // TextColumn::make('baseUnit.name')
                //     ->label('Unit'),

                TextColumn::make('reorder_threshold')
                    ->label('Batas Minimum  ')
                    ->suffix(fn($record) => ' ' . $record->baseUnit?->name),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->getStateUsing(
                        fn($record) =>
                        $record->current_stock <= 0 ? 'Habis' : 'Menipis'
                    )
                    ->colors([
                        'danger' => 'Habis',
                        'warning' => 'Menipis',
                    ]),

                TextColumn::make('updated_at')
                    ->label('Terakhir Update')
                    ->since(),
            ])
            ->emptyStateHeading('Semua stok aman 🎉')
            ->emptyStateDescription('Tidak ada item dengan stok menipis.')
            ->recordUrl(
                fn($record) =>
                ItemResource::getUrl('view', ['record' => $record])
            )
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
