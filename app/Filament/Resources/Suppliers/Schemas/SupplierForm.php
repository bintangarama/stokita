<?php

namespace App\Filament\Resources\Suppliers\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SupplierForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Nama')
                    ->required(),
                TextInput::make('contact_person')
                    ->label('Kontak')
                    ->nullable(),
                TextInput::make('phone')
                    ->label('Telepon')
                    ->nullable()
                    ->tel(),
                TextInput::make('email')
                    ->label('Email')
                    ->nullable()
                    ->email(),
                Textarea::make('address')
                    ->label('Alamat')
                    ->nullable()
                    ->columnSpanFull(),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
