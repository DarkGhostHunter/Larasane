<?php

namespace DarkGhostHunter\Larasane\Facades;

use DarkGhostHunter\Larasane\PendingSanitization;
use Illuminate\Support\Facades\Facade;

/**
 * @method static \DarkGhostHunter\Larasane\PendingSanitization maxLength(int $int)
 * @method static \DarkGhostHunter\Larasane\PendingSanitization allowCode(string ...$extensions)
 * @method static \DarkGhostHunter\Larasane\PendingSanitization hosts(string ...$hosts)
 * @method static \DarkGhostHunter\Larasane\PendingSanitization anyHost()
 * @method static \DarkGhostHunter\Larasane\PendingSanitization enforceHttps(bool $enforce = true)
 * @method static \DarkGhostHunter\Larasane\PendingSanitization imageData(bool $allow = true)
 * @method static \DarkGhostHunter\Larasane\PendingSanitization allowMailto(bool $allow = true)
 * @method static \DarkGhostHunter\Larasane\PendingSanitization tagAttributes(string $tag, string ...$allowedAttributes)
 * @method static \DarkGhostHunter\Larasane\PendingSanitization tagConfig(string $tag, string $key, $value)
 * @method static \DarkGhostHunter\Larasane\PendingSanitization input($input)
 * @method static \Illuminate\Support\Stringable sanitize()
 */
class Sanitizer extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return PendingSanitization::class;
    }
}