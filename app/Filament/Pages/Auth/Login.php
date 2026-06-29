<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Form;
use Illuminate\Validation\ValidationException;

class Login extends \Filament\Auth\Pages\Login
{
    // public function getTitle(): string
    // {
    //     return 'Masuk ke STOKITA';
    // }
    public function getHeading(): string
    {
        return 'Selamat Datang!';
    }

    public function getSubheading(): string
    {
        return 'Silakan masuk untuk mengelola persediaan dan pesanan';
    }

    public function mount(): void
    {
        parent::mount();

        $this->form->fill([
            'email' => 'demo@stokita.com',
            'password' => 'demo',
            'remember' => true,
        ]);
    }
}
