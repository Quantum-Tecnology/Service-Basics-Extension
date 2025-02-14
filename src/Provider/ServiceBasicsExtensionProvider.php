<?php

namespace GustavoSantarosa\ServiceBasicsExtension\Provider;

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
        $this->app->alias('service', \GustavoSantarosa\ServiceBasicsExtension\Middleware\ServiceMiddleware::class);
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
    }
}
