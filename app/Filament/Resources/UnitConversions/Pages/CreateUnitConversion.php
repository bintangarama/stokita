<?php

namespace App\Filament\Resources\UnitConversions\Pages;

use App\Filament\Resources\UnitConversions\UnitConversionResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreateUnitConversion extends CreateRecord
{
    protected static string $resource = UnitConversionResource::class;

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        try {
            return static::getModel()::create($data);
        } catch (\Exception $e) {
            Notification::make()
                ->title('Gagal menyimpan konversi')
                ->body($e->getMessage())
                ->danger()
                ->send();

            $this->halt(); // hentikan proses simpan
        }
    }
}
