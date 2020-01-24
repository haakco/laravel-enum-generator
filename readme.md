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

[ico-version]: https://img.shields.io/packagist/v/haakco/laravelenumgenerator.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/haakco/laravelenumgenerator.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/haakco/laravelenumgenerator/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/haakco/laravelenumgenerator
[link-downloads]: https://packagist.org/packages/haakco/laravelenumgenerator
[link-travis]: https://travis-ci.org/haakco/laravelenumgenerator
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/haakco
[link-contributors]: ../../contributors
