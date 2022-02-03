<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
//        \DB::listen(function($query) {
//            $sql = $query->sql;
//            $bindings = $query->bindings;
//            $executionTime = $query->time;
//            debug($sql);
//            // do something with the above. Log it, stream it via pusher, etc
//        });
    }
}
