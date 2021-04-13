<?php

namespace Tests;

use DarkGhostHunter\Larasane\Facades\Sanitizer;
use DarkGhostHunter\Larasane\LarasaneServiceProvider;
use DarkGhostHunter\Larasane\PendingSanitization;
use HtmlSanitizer\SanitizerBuilder;
use Illuminate\Support\Facades\File;
use Orchestra\Testbench\TestCase;

class ServiceProviderTest extends TestCase
{
    use RegistersPackage;

    public function test_register_config(): void
    {
        static::assertArrayHasKey(LarasaneServiceProvider::class, $this->app->getLoadedProviders());
    }

    public function test_registers_facade(): void
    {
        static::assertInstanceOf(PendingSanitization::class, Sanitizer::getFacadeRoot());
    }

    public function test_publishes_config(): void
    {
        static::assertEquals(include(__DIR__ . '/../config/larasane.php'), config('larasane'));
    }

    public function test_registers_pending_sanitization(): void
    {
        $this->artisan(
            'vendor:publish',
            [
                '--provider' => 'DarkGhostHunter\Larasane\LarasaneServiceProvider',
                '--tag' => 'config',
            ]
        )->execute();

        static::assertFileEquals(base_path('config/larasane.php'), __DIR__ . '/../config/larasane.php');
    }

    public function test_registers_bindings(): void
    {
        static::assertArrayHasKey(SanitizerBuilder::class, $this->app->getBindings());
        static::assertArrayHasKey(PendingSanitization::class, $this->app->getBindings());
    }
}