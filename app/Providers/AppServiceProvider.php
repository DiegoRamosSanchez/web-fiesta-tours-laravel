<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Contact;
use App\Models\Supplier;
use App\Models\CategorySupplier;
use App\Models\Destination;
use Illuminate\Database\Schema\Builder;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        Builder::defaultStringLength(191);

        // PKs personalizadas
        Route::bind('client', function ($value) {
            return Client::where('id_client', $value)->firstOrFail();
        });

        Route::bind('contact', function ($value) {
            return Contact::where('id_contacts', $value)->firstOrFail();
        });

        Route::bind('destination', function ($value) {
            return Supplier::where('id_destinations', $value)->firstOrFail();
        });

        Route::bind('category', function ($value) {
            return CategorySupplier::where('id_categories_suppliers', $value)->firstOrFail();
        });

        Route::bind('supplier', function ($value) {
            return Supplier::where('id_supplier', $value)->firstOrFail();
        });
    }
}
