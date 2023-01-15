<?php

namespace Valinteca\PackageName;

use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    public function register()
    {
        /**
         * Config
         */
        $this->publishes([
            __DIR__ . '/../config/package-name.php' => config_path('package-name.php'),
        ]);

        $this->mergeConfigFrom(
            __DIR__ . '/../config/package-name.php', 'package-name'
        );
    }

    public function boot()
    {
        /**
         * Routes
         */
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        /**
         * Migrations
         */
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        /**
         * Views
         */
        $this->loadViewsFrom(__DIR__ . '/../resources/views', 'package-name');
        
        $this->publishes([
            __DIR__ . '/../resources/views' => resource_path('views/vendor/package-name'),
        ]);
    
        /**
         * Translations
         */
        $this->loadTranslationsFrom(__DIR__ . '/../lang', 'package-name');
 
        $this->publishes([
            __DIR__ . '/../lang' => $this->app->langPath('vendor/package-name'),
        ]);

        /**
         * Public assets
         */
        $this->publishes([
            __DIR__ . '/../public' => public_path('vendor/package-name'),
        ], 'public');    
    }
}