<?php

namespace App\Filament\Resources\Units\Pages;

use App\Filament\Resources\Units\UnitResource;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUnit extends CreateRecord
{
    protected static string $resource = UnitResource::class;

    // protected function afterCreate(): void
    // {
    //     $brand = $this->record;
    //     Notification::make('NNN')
    //         ->title('Test DB Notification')
    //         ->send()
    //         ->sendToDatabase(auth()->user());
    // }
}
