<?php

namespace App\Providers;

use App\Models\Production;
use Illuminate\Support\ServiceProvider;
use App\Models\PurchaseItem;
use App\Observers\PurchaseItemObserver;
use App\Models\Purchase;
use App\Observers\ProductionObserver;
use App\Observers\PurchaseObserver;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

// use Illuminate\Notifications\DatabaseNotification;
// use Illuminate\Support\Str;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        PurchaseItem::observe(PurchaseItemObserver::class);
        Purchase::observe(PurchaseObserver::class);
        Production::observe(ProductionObserver::class);

        // Cek jika sedang diakses via Ngrok, paksa HTTPS
        // if (str_contains(request()->url(), 'ngrok-free.app')) {
        //     URL::forceScheme('https');
        // }
        // Menyuntikkan meta theme-color ke dalam Head Filament
        FilamentView::registerRenderHook(
            PanelsRenderHook::HEAD_END,
            fn(): string => Blade::render('
            <meta name="theme-color" content="#F04D33" media="(prefers-color-scheme: light)" />

            <meta name="theme-color" content="#F04D33" media="(prefers-color-scheme: dark)" />
        ')
        );
    }
}
