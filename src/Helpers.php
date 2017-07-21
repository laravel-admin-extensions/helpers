<?php

namespace Encore\Admin\Helpers;

use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Extension;
use Illuminate\Support\Facades\Route;

class Helpers extends Extension
{
    /**
     * Bootstrap this package.
     *
     * @return void
     */
    public static function boot()
    {
        static::registerRoutes();
    }

    /**
     * Register routes for laravel-admin.
     *
     * @return void
     */
    public static function registerRoutes()
    {
        /* @var \Illuminate\Routing\Router $router */
        Route::group(['prefix' => config('admin.route.prefix')], function ($router) {

            $attributes = array_merge([
                'prefix'     => 'helpers',
                'middleware' => config('admin.route.middleware'),
            ], static::config('route', []));

            /* @var \Illuminate\Routing\Router $router */
            $router->group($attributes, function ($router) {

                /* @var \Illuminate\Routing\Router $router */
                $router->get('terminal/database', 'Encore\Admin\Helpers\Controllers\TerminalController@database');
                $router->post('terminal/database', 'Encore\Admin\Helpers\Controllers\TerminalController@runDatabase');
                $router->get('terminal/artisan', 'Encore\Admin\Helpers\Controllers\TerminalController@artisan');
                $router->post('terminal/artisan', 'Encore\Admin\Helpers\Controllers\TerminalController@runArtisan');
                $router->get('scaffold', 'Encore\Admin\Helpers\Controllers\ScaffoldController@index');
                $router->post('scaffold', 'Encore\Admin\Helpers\Controllers\ScaffoldController@store');
                $router->get('routes', 'Encore\Admin\Helpers\Controllers\RouteController@index');
            });
        });
    }

    public static function import()
    {
        $lastOrder = Menu::max('order');

        $root = [
            'parent_id' => 0,
            'order'     => $lastOrder++,
            'title'     => 'Helpers',
            'icon'      => 'fa-gears',
            'uri'       => '',
        ];

        $root = Menu::create($root);

        $menus = [
            [
                'title'     => 'Scaffold',
                'icon'      => 'fa-keyboard-o',
                'uri'       => 'helpers/scaffold',
            ],
            [
                'title'     => 'Database terminal',
                'icon'      => 'fa-database',
                'uri'       => 'helpers/terminal/database',
            ],
            [
                'title'     => 'Laravel artisan',
                'icon'      => 'fa-terminal',
                'uri'       => 'helpers/terminal/artisan',
            ],
            [
                'title'     => 'Routes',
                'icon'      => 'fa-list-alt',
                'uri'       => 'helpers/routes',
            ]
        ];

        foreach ($menus as $menu) {

            $menu['parent_id'] = $root->id;
            $menu['order'] = $lastOrder++;

            Menu::create($menu);
        }

        // Add a permission.
        Permission::create([
            'name'          => 'Admin helpers',
            'slug'          => 'ext.helpers',
            'http_path'     => admin_base_path('helpers/*'),
        ]);
    }
}