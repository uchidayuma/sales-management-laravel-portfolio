<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Step;
use View;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register()
    {
    }

    /**
     * Bootstrap services.
     */
    public function boot()
    {
        View::composer('*', function ($view) {
            $step = new Step();
            if (isAdmin()) {
                $view->with('user', \Auth::user())->with('tasks', $step->adminViewSteps());
            } else {
                $view->with('user', \Auth::user())->with('tasks', $step->viewSteps())->with('totalTasks', $step->totalTasks());
            }
        });
    }
}
