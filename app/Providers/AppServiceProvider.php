<?php

namespace Mtvtd\Deploy\Providers;

use Mtvtd\Deploy\Support\Config;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(Config::class, function () {
            return new Config(Config::loadLocal());
        });
    }
}
