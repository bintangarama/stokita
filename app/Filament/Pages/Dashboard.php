<?php

namespace App\Filament\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Filament\Resources\Productions\ProductionResource;
use App\Filament\Resources\Purchases\PurchaseResource;
use App\Models\Order;
use App\Models\Production;
use App\Models\Purchase;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Str;

class Dashboard extends BaseDashboard
{
    // Mengubah judul besar di tengah halaman
    // protected ?string $heading = 'Beranda Utama';


    public function getHeading(): string
    {
        // Mengambil jam saat ini (00-23)
        $hour = now()->hour;
        $fullName = auth()->user()->name;

        // Mengambil teks sebelum spasi pertama (Nama Depan)
        $firstName = Str::before($fullName, ' ');

        if ($hour >= 5 && $hour < 11) {
            $salam = 'Selamat Pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $salam = 'Selamat Siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $salam = 'Selamat Sore';
        } else {
            $salam = 'Selamat Malam';
        }

        return "{$salam}, {$firstName}! 👋";
        // return "Jam Server: " . now()->format('H:i') . " - Jam User: " . $hour;
        // $user = Auth::user();

        // return "Selamat Datang, " . $user->name . "! 😘❤️";
    }

    // Optional: Jika ingin menambahkan sub-heading di bawahnya
    public function getSubheading(): ?string
    {
        return 'Senang melihat Anda kembali hari ini.';
    }

    // Mengubah teks di Sidebar dan Breadcrumb
    protected static ?string $title = 'Beranda';

    // Jika ingin ikon navigasi juga berubah
    // protected static ?string $navigationIcon =   'heroicon-o-home';
    protected function getHeaderActions(): array
    {
        return [
            Action::make('createPurchase')
                ->visible(fn() => auth()->user()->can('create purchases', Purchase::class))
                ->label('Pembelian')
                ->icon('heroicon-m-plus')
                ->color('primary')
                ->url(PurchaseResource::getUrl('create')),

            Action::make('createProduction')
                ->visible(fn() => auth()->user()->can('create productions', Production::class))
                ->label('Produksi')
                ->icon('heroicon-m-plus')
                ->color('success')
                ->url(ProductionResource::getUrl('create')),

            Action::make('createOrder')
                ->visible(fn() => auth()->user()->can('create orders', Order::class))
                ->label('Pesanan')
                ->icon('heroicon-m-plus')
                ->color('warning')
                ->url(OrderResource::getUrl('create')),
        ];
    }
}
