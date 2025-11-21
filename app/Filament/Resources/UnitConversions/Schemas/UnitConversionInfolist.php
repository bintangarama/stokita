<?php

namespace App\Filament\Resources\UnitConversions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UnitConversionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('unit_from_id')
                    ->numeric(),
                TextEntry::make('unit_to_id')
                    ->numeric(),
                TextEntry::make('factor')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
