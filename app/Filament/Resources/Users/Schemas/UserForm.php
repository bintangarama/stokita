<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Spatie\Permission\Models\Role;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Pengguna')
                    ->schema([
                        TextInput::make('name')
                            ->maxLength(255)
                            ->required(),
                        TextInput::make('email')
                            ->label('Email address')
                            ->email()
                            ->required()
                            ->unique(ignoreRecord: true),
                        // TextInput::make('role')
                        //     ->required()
                        //     ->default('staff'),
                        // DateTimePicker::make('email_verified_at'),
                        TextInput::make('password')
                            ->password()
                            ->required(fn($record) => $record === null)
                            ->dehydrated(fn($state) => filled($state))
                            ->confirmed(),
                        TextInput::make('password_confirmation')
                            ->password()
                            ->dehydrated(false),
                    ])
                    ->columns(2),
                Section::make('Role Pengguna')
                    ->schema([
                        Select::make('roles')
                            ->label('Role')
                            ->multiple(false)
                            ->relationship('roles', 'name')
                            // ->options(Role::pluck('name', 'name'))
                            ->required()
                            ->visible(fn() => auth()->user()->hasRole('admin')),

                    ])

            ]);
    }
}
