<?php

namespace App\Filament\Resources\Items\Pages;

use App\Filament\Resources\Items\ItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateItem extends CreateRecord
{
    protected static string $resource = ItemResource::class;

    // public function getTitle(): string
    // {
    //     return 'Tambah Barang';
    // }

    // // public function getBreadcrumb(): string
    // // {
    // //     return 'Tambah';
    // // }
}
