<?php

namespace App\Providers;

use App\Models\Market;
use App\Models\MarketCategory;
use App\Models\MarketProduct;
use App\Models\Product;
use App\Models\ProductCategory;
use App\Models\Unit;
use App\Policies\MarketCategoryPolicy;
use App\Policies\MarketPolicy;
use App\Policies\MarketProductPolicy;
use App\Policies\ProductCategoryPolicy;
use App\Policies\ProductPolicy;
use App\Policies\UnitPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

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
        Gate::policy(ProductCategory::class, ProductCategoryPolicy::class);
        Gate::policy(Unit::class, UnitPolicy::class);
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(MarketCategory::class, MarketCategoryPolicy::class);
        Gate::policy(Market::class, MarketPolicy::class);
        Gate::policy(MarketProduct::class, MarketProductPolicy::class);
    }
}
