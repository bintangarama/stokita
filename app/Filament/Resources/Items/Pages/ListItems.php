<?php

namespace App\Filament\Resources\Items\Pages;

use App\Filament\Resources\Items\ItemResource;
use App\Filament\Resources\Items\Widgets\ItemStats;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListItems extends ListRecords
{
    protected static string $resource = ItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ItemStats::class,
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),

            'raw_material' => Tab::make('Raw Material')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('type', 'raw_material')
                ),

            'component' => Tab::make('Component')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('type', 'component')
                ),

            'finished_good' => Tab::make('Finished Good')
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('type', 'finish_good')
                ),
        ];
    }
}
