<?php

namespace App\Filament\Resources\Recipes\RelationManagers;

use App\Filament\Resources\Recipes\RecipeResource;
use App\Models\Item;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class IngredientsRelationManager extends RelationManager
{
    protected static string $relationship = 'ingredients';

    protected static ?string $relatedResource = RecipeResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('ingredientItem.name')
            ->columns([
                TextColumn::make('ingredientItem.name')
                    ->label('Bahan')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('unit.name')
                    ->label('Satuan')
                    ->sortable(),

                TextColumn::make('qty')
                    ->label('Qty')
                    ->numeric(4)
                    ->sortable(),

                TextColumn::make('ingredientItem.average_cost')
                    ->label('Harga Rata-rata')
                    ->money('IDR', true)
                    ->sortable(),

                TextColumn::make('line_cost')
                    ->label('Perkiraan Biaya')
                    ->getStateUsing(
                        fn($record) =>
                        (float) ($record->ingredientItem?->average_cost ?? 0) * (float) ($record->qty ?? 0)
                    )
                    ->money('IDR', true),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('Tambah Bahan')
                    ->mutateFormDataUsing(function (array $data): array {
                        // pastikan line_total tersimpan sesuai qty * average_cost
                        $item = Item::find($data['ingredient_item_id']);
                        $data['calculated_cost'] = ($item?->average_cost ?? 0) * ($data['qty'] ?? 0);
                        return $data;
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->paginated(false)
        ;
    }
}
