<?php

namespace App\Filament\Resources\StockMovements\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class StockMovementInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('item.name')
                    ->numeric(),
                TextEntry::make('movement_type'),
                TextEntry::make('reference_table'),
                TextEntry::make('reference_id')
                    ->numeric(),
                TextEntry::make('qty')
                    ->numeric(),
                TextEntry::make('unit.name')
                    ->numeric(),
                TextEntry::make('unit_cost')
                    ->numeric(),
                TextEntry::make('created_by')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
