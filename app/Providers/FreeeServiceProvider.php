<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Extensions\SampleSessionGuard;
use App\Extensions\FreeeUserProvider;
use Illuminate\Support\Facades\Auth;

class FreeeServiceProvider extends ServiceProvider
{
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
        //$this->registerPolicies();

        Auth::extend('freee', function ($app, $name, array $config) {
            return new SampleSessionGuard(
                $name,
                Auth::createUserProvider($config['provider']),
                $app['session.store']
            );
        });

        Auth::provider('freee', function ($app, array $config) {
            return new FreeeProvider();
        });
    }
}