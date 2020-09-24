<?php

namespace Encore\Admin\Helpers;

use Encore\Admin\Assets;
use Illuminate\Support\ServiceProvider;

class HelpersServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'laravel-admin-helpers');

        Helpers::boot();

        Assets::define('slimscroll', [
            'deps' => 'jquery',
            'js' => 'https://cdn.jsdelivr.net/npm/jquery-slimscroll@1.3.8/jquery.slimscroll.min.js',
        ]);
    }
}
