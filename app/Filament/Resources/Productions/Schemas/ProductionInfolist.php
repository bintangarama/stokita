<?php

namespace App\Filament\Resources\Productions\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ProductionInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('producedItem.name')
                    ->numeric(),
                TextEntry::make('recipe.name')
                    ->numeric(),
                TextEntry::make('produced_unit_id')
                    ->numeric(),
                TextEntry::make('produced_qty')
                    ->numeric(),
                TextEntry::make('cost_total')
                    ->numeric(),
                TextEntry::make('overhead_cost')
                    ->numeric(),
                TextEntry::make('selling_price')
                    ->numeric(),
                TextEntry::make('produced_by')
                    ->numeric(),
                TextEntry::make('produced_at')
                    ->dateTime(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
            ]);
    }
}
