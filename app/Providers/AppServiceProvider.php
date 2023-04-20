<?php

namespace App\Providers;

use App\Support\MTVTDConfig;
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
        $this->app->bind(MTVTDConfig::class, function () {
            return new MTVTDConfig(MTVTDConfig::loadLocal());
        });
    }
}
