<?php

namespace App\Filament\Resources\Productions\Schemas;

use App\Models\Item;
use App\Models\Recipe;
use App\Models\Unit;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ProductionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('produced_item_id')
                    ->label('Item Hasil Produksi')
                    ->options(Item::whereIn('type', ['component', 'finish_good'])->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('recipe_id')
                    ->label('Resep')
                    ->options(Recipe::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->reactive()
                    ->helperText(fn($get) => $get('recipe_id') ? 'Components akan otomatis ter-generate dari resep saat disimpan.' : '')
                    ->required(),
                Select::make('produced_unit_id')
                    ->label('Satuan Hasil')
                    ->options(Unit::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('produced_qty')
                    ->label('Jumlah Produksi')
                    ->numeric()
                    ->required(),
                TextInput::make('cost_total')
                    ->required()
                    ->numeric()
                    ->default(0.0),
                TextInput::make('overhead_cost')
                    ->label('Biaya Overhead')
                    ->numeric()
                    ->default(0.0),
                TextInput::make('selling_price')
                    ->label('Harga Jual (opsional)')
                    ->numeric()
                    ->nullable(),
                TextInput::make('produced_by')
                    ->numeric(),
                DateTimePicker::make('produced_at')
                    ->required(),
                Textarea::make('notes')
                    ->label('Catatan Produksi')
                    ->rows(2),
                // ->columnSpanFull(),
            ]);
    }
}
