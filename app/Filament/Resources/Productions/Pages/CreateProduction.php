<?php

namespace App\Filament\Resources\Productions\Pages;

use App\Filament\Resources\Productions\ProductionResource;
use App\Services\ProductionService;
// use App\Models\ProductionComponent;
// use App\Models\Recipe;
// use App\Models\StockMovement;
// use App\Services\UnitConversionService;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateProduction extends CreateRecord
{
    protected static string $resource = ProductionResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return ProductionService::produce(
            recipeId: $data['recipe_id'],
            outputQty: $data['produced_qty'],
            userId: auth()->id(),
            overheadCost: $data['overhead_cost'] ?? null,
            outputUnitId: $data['produced_unit_id'],
        );
    }
}
