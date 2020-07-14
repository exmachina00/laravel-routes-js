# Laravel routes to JS
Convert Laravel's routes to `json` file to generate URL in Javascript

## Features
- Supports Laravel 5.8 and 6.x.

* Allow to specify what routes to be included/excluded.

## Installation

```shell
composer require exmachina/laravel-route-to-js
```

In your Laravel `app/config.php`, add the service provider:

```php
ExMachina\JSRoutes\Providers\JsRoutesServiceProvider::class
```

## Configuration

First, publish the default package's configuration:

```php
php artisan vendor:publish --provider="ExMachina\JSRoutes\Providers\JsRoutesServiceProvider"
```

The configuration will be published to `config/route-js.php`.



Element|Type|Default|Description
:-:|:-:|:-:|---
`dir`|*string*|resources/js/routes|Directory where the files will be saved.
`route.patterns`|array|[ ]|Routes to be included when generating route list.<br />*element type: String literals or RegExp pattern or both*.<br />
`route.exclude`|*boolean*|false|When set to **true**, exclude the routes provided in `routes.patterns`




## Usage

### Generating the routes

```shell
php artisan js-route:generate
```

This command will generate 2 files, the route list(`laravel-routes.json`) and the js(`laravel-routes.js`) file for the functionality. The default directory is `resources/js/routes`. 

### Options

|     Option     | Description                                                  |
| :------------: | ------------------------------------------------------------ |
|    `--dir`     | Directory where the files will be saved                      |
|   `--routes`   | Routes to be included when generating route list.<br />String literals or RegExp pattern or both.<br /><br />Can have multiple options for different patterns. <br />e.g(--routes=admin\* --routes=public.index) |
|  `--exclude`   | When provided, exclude the routes provided in<br / `--routes` option or <br />`routes.patterns` in `config/route-js.ph` |
| `--exclude-js` | Only route list will be generate. The JS file that will act upon it will be excluded. |



## JS Usage

```javascript
import { Route } from 'path/to/laravel-route.js';
```

### Get URL using named route

```javascript
Route.get('welcome');
```

### Route with parameters

Parameter names will be the same in what you provided in your Laravel routes.

```javascript
# This will be equivalent to laravel route entry: 
#  Route::get('profile/{id}').name('profile.edit')
Route.get('profile.edit', {id: 1})

# Laravel route: Route::post('profile/{id}/address/{address_id}').name('address.edit')
Route.get('address.edit', {id: 1, address_id: 1004})
```



### Route list

You can change the route list by calling `setRoutes` method of `Route` instance

```javascript
import { Route } from 'path/to/laravel-route.js';

Route.setRoutes( [ { 'route-name': 'url', parameters: []} ] )
```






