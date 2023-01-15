<?php

namespace Valinteca\Aramex;

use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    public function register()
    {
        /**
         * Config
         */
        $this->publishes([
            __DIR__ . '/../config/aramex.php' => config_path('aramex.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/../config/aramex.php', 'aramex'
        );
    }

    public function boot()
    {
        /**
         * Routes
         */
        // $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        /**
         * Migrations
         */
        // $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        /**
         * Views
         */
        // $this->loadViewsFrom(__DIR__ . '/../resources/views', 'aramex');
        
        // $this->publishes([
        //     __DIR__ . '/../resources/views' => resource_path('views/vendor/aramex'),
        // ]);
    
        /**
         * Translations
         */
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'aramex');
 
        $this->publishes([
            __DIR__ . '/../lang' => $this->app->langPath('vendor/aramex'),
        ]);

        /**
         * Public assets
         */
        // $this->publishes([
        //     __DIR__ . '/../public' => public_path('vendor/aramex'),
        // ], 'public');    
    }
}