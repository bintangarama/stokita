<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Schemas\Schema;

class Profile extends BaseEditProfile
{
    protected static ?string $navigationLabel = 'Profil Saya';

    protected static ?string $title = 'Profil Saya';

    public static function shouldRegisterNavigation(): bool
    {
        return false; // tidak muncul di sidebar
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('avatar_url')
                    ->label('Foto Profil')
                    ->image()
                    ->disk('public')
                    ->directory('avatars')
                    ->circleCropper()
                    ->imageEditor()
                    ->maxSize(2048),

                TextInput::make('name')
                    ->label('Nama')
                    ->required(),

                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required(),

                TextInput::make('password')
                    ->label('Kata Sandi Baru')
                    ->password()
                    ->nullable()
                    ->dehydrateStateUsing(
                        fn($state) =>
                        filled($state) ? bcrypt($state) : null
                    ),

                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Kata Sandi')
                    ->password()
                    ->same('password'),
            ]);
    }
}
