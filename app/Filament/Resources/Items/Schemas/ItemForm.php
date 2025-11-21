<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ItemForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('sku')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->label('SKU'),
                TextInput::make('name')
                    ->label('Nama Item')
                    ->maxLength(255)
                    ->required(),
                Select::make('type')
                    ->label('Tipe')
                    ->options([
                        'raw_material' => 'Raw Material',
                        'component' => 'Component',
                        'finish_good' => 'Finish Good',
                    ])
                    ->required(),
                Select::make('base_unit_id')
                    ->label('Satuan Dasar')
                    ->relationship('baseUnit', 'name')
                    ->searchable()
                    ->required(),
                Toggle::make('track_stock')
                    ->required()
                    ->default(true),
                TextInput::make('reorder_threshold')
                    ->label('Minimal Stok')
                    ->numeric()
                    ->default(10),
                TextInput::make('current_stock')
                    ->label('Stok Saat Ini')
                    ->numeric()
                    ->default(0.0)
                    ->disabled(),
                TextInput::make('average_cost')
                    ->label('Harga Rata-rata / HPP')
                    ->numeric()
                    ->default(0.0)
                    ->disabled(),
                TextInput::make('last_purchase_price')
                    ->numeric(),
                TextInput::make('selling_price')
                    ->label('Harga Jual')
                    ->numeric()
                    ->default(0),
            ]);
    }
}
