<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\User;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register()
    {
        // 開発環境ならばSQL Logをlaravel-logに出力
        // if (app()->isLocal()) {
        //     \DB::listen(function ($query) {
        //         $sql = $query->sql;
        //         for ($i = 0; $i < count($query->bindings); ++$i) {
        //             $sql = preg_replace("/\?/", $query->bindings[$i], $sql, 1);
        //         }
        //         \Log::debug('SQL', ['time' => sprintf('%.2f ms', $query->time), 'sql' => $sql]);
        //     });
        // }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        View::share('user', \Auth::user());
        View::share('tasks');
        Paginator::useBootstrap();
    }
}
