<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ValidatorServiceProvider extends ServiceProvider {

    public function boot() {
        $this->app['validator']->extend('customEmail', function ($attribute, $value, $parameters) {

            return $value == 'test';
        });
    }

    public function register() {
        //
    }

}
