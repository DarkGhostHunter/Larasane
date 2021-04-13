<?php

namespace DarkGhostHunter\Larasane;

use HtmlSanitizer\SanitizerBuilder;
use Illuminate\Support\ServiceProvider as LaravelServiceProvider;

class LarasaneServiceProvider extends LaravelServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/larasane.php', 'larasane');

        $this->app->singleton(SanitizerBuilder::class, static function () {
            return SanitizerBuilder::createDefault();
        });

        $this->app->bind(PendingSanitization::class, static function ($app) {
            return new PendingSanitization($app[SanitizerBuilder::class], $app['config']);
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([__DIR__ . '/../config/larasane.php' => $this->app->configPath('larasane.php')], 'config');
        }
    }
}