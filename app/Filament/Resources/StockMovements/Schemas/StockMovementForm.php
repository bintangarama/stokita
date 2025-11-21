<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StockMovementForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('item_id')
                    ->relationship('item', 'name')
                    ->required(),
                Select::make('movement_type')
                    ->options([
            'purchase_in' => 'Purchase in',
            'production_out' => 'Production out',
            'production_in' => 'Production in',
            'sale_out' => 'Sale out',
            'adjustment' => 'Adjustment',
            'transfer_in' => 'Transfer in',
            'transfer_out' => 'Transfer out',
        ])
                    ->required(),
                TextInput::make('reference_table'),
                TextInput::make('reference_id')
                    ->numeric(),
                TextInput::make('qty')
                    ->required()
                    ->numeric(),
                Select::make('unit_id')
                    ->relationship('unit', 'name')
                    ->required(),
                TextInput::make('unit_cost')
                    ->numeric(),
                TextInput::make('created_by')
                    ->numeric(),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
