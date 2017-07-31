laravel-admin-ext/helpers
=========================

[![StyleCI](https://styleci.io/repos/97667375/shield?branch=master)](https://styleci.io/repos/97667375)
[![Packagist](https://img.shields.io/packagist/l/laravel-admin-ext/helpers.svg?maxAge=2592000)](https://packagist.org/packages/laravel-admin-ext/helpers)
[![Total Downloads](https://img.shields.io/packagist/dt/laravel-admin-ext/helpers.svg?style=flat-square)](https://packagist.org/packages/laravel-admin-ext/helpers)

[Demo](http://120.26.143.106/admin) use `username/password:admin/admin`

## Installation

```
$ composer require laravel-admin-ext/helpers
```

Open `app/Providers/AppServiceProvider.php`, and call the `Helpers::boot` method within the `boot` method:

```php
<?php

namespace App\Providers;

use Encore\Admin\Helpers\Helpers;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Helpers::boot();
    }
}
```

```
$ php artisan admin:import helpers
```

## Usage

See [wiki](http://z-song.github.io/laravel-admin/#/en/helpers)

License
------------
Licensed under [The MIT License (MIT)](LICENSE).
