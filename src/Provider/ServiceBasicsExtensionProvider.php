<?php

namespace GustavoSantarosa\EnumBasicsExtension\Provider;

use Illuminate\Support\ServiceProvider;
use GustavoSantarosa\ServiceBasicsExtension\BaseService;

class ServiceBasicsExtensionProvider extends ServiceProvider
{
    public $bindings = [
        ServerProvider::class => BaseService::class,
    ];

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
