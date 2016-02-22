<?php

namespace App\Providers;

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
        app('Dingo\Api\Auth\Auth')->extend('basic', function ($app) {
            return new \Dingo\Api\Auth\Provider\Basic($app['auth'], 'username');
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
//        // TODO: Comment out code once our package is developed. Also
//        // TODO: remove matching entry in 'require-dev' section of main
//        // TODO: 'composer.json' file => "jeroen-g/laravel-packager": "dev-master"
//        // Manually registering provider only if the environment is set to
//        // development. That prevents a loading failure in PROD when the
//        // package is not present.
        if ($this->app->environment('development')) {
            $this->app->register('Laracasts\Generators\GeneratorsServiceProvider');
//            $this->app->register('JeroenG\Packager\PackagerServiceProvider');
        }
    }
}
