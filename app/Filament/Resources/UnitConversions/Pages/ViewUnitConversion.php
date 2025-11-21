<?php

namespace App\Filament\Resources\UnitConversions\Pages;

use App\Filament\Resources\UnitConversions\UnitConversionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewUnitConversion extends ViewRecord
{
    protected static string $resource = UnitConversionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
