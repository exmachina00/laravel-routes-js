<?php

namespace ExMachina\LaravelRouteJs\Providers;

use ExMachina\LaravelRouteJs\JSRoutes;
use ExMachina\LaravelRouteJs\Commands\CreateJsRoute;

use Illuminate\Support\ServiceProvider;

class JsRoutesServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $configPath = __DIR__ . '/../Config/route-js.php';

        $this->publishes([
            $configPath => config_path('route-js.php'),
        ]);

        $this->mergeConfigFrom($configPath, 'route-js');
    }

    public function register()
    {
        $this->app->singleton('route.js', function ($app) {
            return new CreateJsRoute(new JSRoutes);
        });
        
        $this->commands('route.js');
    }

    public function provides()
    {
        return ['routes.js'];
    }
}
