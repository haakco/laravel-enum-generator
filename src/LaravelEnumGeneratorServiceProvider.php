<?php

namespace HaakCo\LaravelEnumGenerator;

use HaakCo\LaravelEnumGenerator\Console\Commands\ModelEnumCreate;
use Illuminate\Support\ServiceProvider;

class LaravelEnumGeneratorServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        // Publishing is only necessary when using the CLI.
        if ($this->app->runningInConsole()) {
            $this->bootForConsole();
        }
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/enum-generator.php', 'laravelenumgenerator');

        // Register the service the package provides.
        $this->app->singleton('laravelenumgenerator', function ($app) {
            return new LaravelEnumGenerator();
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return ['laravelenumgenerator'];
    }

    /**
     * Console-specific booting.
     *
     * @return void
     */
    protected function bootForConsole()
    {
        // Publishing the configuration file.
        $this->publishes([
            __DIR__ . '/../config/enum-generator.php' => config_path('enum-generator.php'),
        ], 'laravelenumgenerator.config');

        $this->loadViewsFrom(__DIR__.'/views', 'enum-generator');

        // Registering package commands.
         $this->commands([
             ModelEnumCreate::class
         ]);
    }
}
