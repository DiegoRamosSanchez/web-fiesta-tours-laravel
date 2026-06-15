<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Contact;
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
    }
}
