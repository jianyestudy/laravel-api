<?php

namespace QCS\LaravelApi\Providers;


use Illuminate\Support\ServiceProvider;

class LaravelApiProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //发布配置文件
        $this->publishes([
            __DIR__.'/../config.php' => config_path('laravel-api.php'),
        ], 'laravel-api');
    }
}
