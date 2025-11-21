<?php

namespace App\Filament\Resources\Items\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ItemInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('sku')
                    ->label('SKU'),
                TextEntry::make('name'),
                TextEntry::make('type'),
                TextEntry::make('base_unit_id')
                    ->numeric(),
                IconEntry::make('track_stock')
                    ->boolean(),
                TextEntry::make('reorder_threshold')
                    ->numeric(),
                TextEntry::make('current_stock')
                    ->numeric(),
                TextEntry::make('average_cost')
                    ->numeric(),
                TextEntry::make('last_purchase_price')
                    ->numeric(),
                TextEntry::make('selling_price')
                    ->numeric(),
                TextEntry::make('created_at')
                    ->dateTime(),
                TextEntry::make('updated_at')
                    ->dateTime(),
                TextEntry::make('deleted_at')
                    ->dateTime(),
            ]);
    }
}
