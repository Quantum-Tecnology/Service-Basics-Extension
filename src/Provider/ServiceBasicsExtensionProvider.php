<?php

namespace QuantumTecnology\ServiceBasicsExtension\Provider;

use Illuminate\Support\ServiceProvider;

class ServiceBasicsExtensionProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->alias('service', \QuantumTecnology\ServiceBasicsExtension\Middleware\ServiceMiddleware::class);
        $this->mergeConfigFrom(
            __DIR__.'/../config/servicebase.php',
            'servicebase'
        );
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/servicebase.php' => config_path('servicebase.php'),
        ], 'config');
    }
}
