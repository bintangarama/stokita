<?php

namespace App\Filament\Resources\Units\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UnitForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label('Kode Satuan')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(20),
                TextInput::make('name')
                    ->label('Nama Satuan')
                    ->required()
                    ->maxLength(100),
            ]);
    }
}
