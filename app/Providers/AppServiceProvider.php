<?php

namespace App\Providers;

use App\Models\Production;
use Illuminate\Support\ServiceProvider;
use App\Models\PurchaseItem;
use App\Observers\PurchaseItemObserver;
use App\Models\Purchase;
use App\Observers\ProductionObserver;
use App\Observers\PurchaseObserver;

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
        // PurchaseItem::observe(PurchaseItemObserver::class);
        // Purchase::observe(PurchaseObserver::class);
        Production::observe(ProductionObserver::class);
    }
}
