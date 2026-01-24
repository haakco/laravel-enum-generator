# LaravelEnumGenerator

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require haakco/laravel-enum-generator --dev
```

### Configuration for local environment only

If you wish to enable generators only for your local environment, you should install it via composer using the --dev option like this:

```shell
composer require haakco/laravel-enum-generator --dev
```

Then you'll need to register the provider in `app/Providers/AppServiceProvider.php` file.

```php
public function register()
{
    if ($this->app->environment() == 'local') {
        $this->app->register(\HaakCo\LaravelEnumGenerator\LaravelEnumGeneratorServiceProvider::class);
    }
}
```

## Usage

Copy the config over

```shell
php artisan vendor:publish --provider="HaakCo\LaravelEnumGenerator\LaravelEnumGeneratorServiceProvider"
```

Edit the config file enum-generator.php to specify which tables to use to generate the files.

Now run the following to re-create your models. 

```shell
php artisan modelEnum:create
```

## Configuration

The `config/enum-generator.php` file supports the following options:

### Global Options

| Option | Default | Description |
|--------|---------|-------------|
| `use-enum-format` | `true` | Use PHP 8.1+ enum format (vs legacy class constants) |
| `default-leave-schema` | `false` | Include schema name in generated class name |
| `default-uuid` | `false` | Include UUID field in generated enums |
| `id-field` | `'id'` | Default column name for enum values |
| `name-field` | `'name'` | Default column name for enum case names |
| `default-prepend_class` | `''` | Prefix for generated class names |
| `default-prepend_name` | `''` | Prefix for generated enum case names |
| `enumPath` | `app_path() . '/Models/Enums'` | Output directory for generated files |
| `default-order-by` | `['name', 'id']` | Default ordering for enum cases |

### Per-Table Options

Each table in the `tables` array can have these options:

```php
'tables' => [
    'public.statuses' => [
        'uuid' => false,
        'leave-schema' => true,
        'prepend-class' => 'App',
        'prepend-name' => 'Status',
        'id-field' => 'id',
        'name-field' => 'name',
        'order-by' => ['name' => 'asc'],
        'where' => [
            'is_active' => true,
            'type' => 'public',
        ],
    ],
],
```

### Filtering Rows with `where`

Use the `where` option to filter which database rows become enum cases. This is useful when:

- You have duplicate values and need to filter by another column
- You only want active/enabled records
- You need to limit enums to a specific type or category

```php
'tables' => [
    // Only generate enums for active permissions
    'permissions' => [
        'where' => [
            'is_active' => true,
        ],
    ],

    // Only generate enums where type equals 'system'
    'categories' => [
        'where' => [
            'type' => 'system',
            'deleted_at' => null,
        ],
    ],
],
```

The `where` option accepts an associative array where keys are column names and values are matched with equality (`=`). Multiple conditions are combined with `AND`.

## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Testing

``` bash
$ composer test
```

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email tim@haak.co instead of using the issue tracker.

## Credits

- [Tim Haak][link-author]
- [All Contributors][link-contributors]

## License

mit. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/haakco/laravel-enum-generator.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/haakco/laravel-enum-generator.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/haakco/laravel-enum-generator/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/haakco/laravel-enum-generator
[link-downloads]: https://packagist.org/packages/haakco/laravel-enum-generator
[link-travis]: https://travis-ci.org/haakco/laravel-enum-generator
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/haakco
[link-contributors]: ../../contributors
