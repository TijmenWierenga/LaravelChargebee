<?php

namespace TijmenWierenga\LaravelChargebee;

use Illuminate\Support\ServiceProvider;

class ChargebeeServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Copies the config file to project config directory on: php artisan vendor:publish
        $this->publishes([
            __DIR__.'/Config/chargebee.php' => config_path('chargebee.php'),
        ], 'config');

        // Publishes the migrations into the application's migrations folder
        $this->publishes([
            __DIR__.'/Migrations/' => database_path('migrations'),
        ], 'migrations');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {

    }
}