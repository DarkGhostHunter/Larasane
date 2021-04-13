<?php

namespace Tests;

trait RegistersPackage
{
    protected function getPackageProviders($app)
    {
        return ['DarkGhostHunter\Larasane\LarasaneServiceProvider'];
    }

    protected function getPackageAliases($app)
    {
        return [
            'ReCaptcha' => 'DarkGhostHunter\Larasane\Facades\Sanitizer'
        ];
    }
}