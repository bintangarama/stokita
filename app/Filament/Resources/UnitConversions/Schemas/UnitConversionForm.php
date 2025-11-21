<?php

namespace App\Filament\Resources\UnitConversions\Schemas;

use App\Models\Unit;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UnitConversionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('unit_from_id')
                    ->label('Dari Satuan')
                    ->options(Unit::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                Select::make('unit_to_id')
                    ->label('Ke Satuan')
                    ->options(Unit::pluck('name', 'id'))
                    ->searchable()
                    ->required(),
                TextInput::make('factor')
                    ->label('Faktor Konversi')
                    ->numeric()
                    ->required()
                    ->helperText('Contoh: 1 kilogram = 1000 gram → faktor = 1000'),
            ]);
    }
}
