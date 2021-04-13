<?php

namespace Tests;

use DarkGhostHunter\Larasane\Facades\Sanitizer;
use Orchestra\Testbench\TestCase;

class PendingSanitizationTest extends TestCase
{
    use RegistersPackage;

    public function test_max_length(): void
    {
        $this->app->make('config')->set('larasane.max_length', 4);

        $input = 'This <script></script>should <strong>cut here</strong> or it is bad';

        static::assertEquals('This', Sanitizer::input($input)->sanitize());
        static::assertEquals('This should <strong>cut here</strong>', Sanitizer::maxLength(54)->input($input)->sanitize());
    }

    public function test_overrides_extension(): void
    {
        $this->app->make('config')->set('larasane.allow_code', ['basic', 'table']);

        $input = '<ul><li>I should see this</li></ul> <table>But not this</table>';

        static::assertEquals(
            'I should see this <table>But not this</table>',
            Sanitizer::input($input)->sanitize()
        );

        static::assertEquals(
            '<ul><li>I should see this</li></ul> But not this',
            Sanitizer::input($input)->allowCode('list')->sanitize()
        );
    }

    public function test_default_allows_any_host(): void
    {
        $input = 'Go to <a href="https://www.google.com">Google</a>.';

        static::assertEquals(
            $input,
            Sanitizer::input($input)->sanitize()
        );
    }

    public function test_overrides_allows_any_host(): void
    {
        $input = 'Go to <a href="https://www.google.com">Google</a>.';

        static::assertEquals(
            $input,
            Sanitizer::input($input)->anyHost()->sanitize()
        );

        config()->set('larasane.security.enforce_hosts', []);

        static::assertEquals(
            'Go to <a>Google</a>.',
            Sanitizer::input($input)->anyHost()->sanitize()
        );

    }

    public function test_overrides_hosts_for_list(): void
    {
        $input = 'Go to <a href="https://www.google.com/useful.js">Google</a>.';

        static::assertEquals(
            $input,
            Sanitizer::input($input)->hosts('google.com')->sanitize()
        );

        $input = 'Go to <a href="https://www.malicio.us/code.js">Google</a>.';

        static::assertEquals(
            'Go to <a>Google</a>.',
            Sanitizer::input($input)->hosts('google.com')->sanitize()
        );
    }

    public function test_overrides_host_for_unallowed_links(): void
    {
        $input = 'Go to <a href="https://www.google.com/useful.js">Google</a>.';

        static::assertEquals(
            'Go to <a>Google</a>.',
            Sanitizer::input($input)->disallowLinks()->sanitize()
        );
    }

    public function test_defaults_accepts_https_in_production(): void
    {
        $this->app['env'] = 'production';
        config()->set('larasane.allow_code', ['basic', 'image', 'iframe']);

        $input = 'Go to <a href="http://myapp.com/useful.js">My Site</a>.';

        static::assertEquals(
            'Go to <a href="https://myapp.com/useful.js">My Site</a>.',
            Sanitizer::input($input)->sanitize()
        );

        $input = 'Check my <img src="http://myapp.com/image.png" alt="image" />';

        static::assertEquals(
            'Check my <img src="https://myapp.com/image.png" alt="image" />',
            Sanitizer::input($input)->sanitize()
        );

        $input = 'Look into <iframe src="http://myapp.com/useful"></iframe>';

        static::assertEquals(
            'Look into <iframe src="https://myapp.com/useful"></iframe>',
            Sanitizer::input($input)->sanitize()
        );
    }

    public function test_does_not_enforces_http(): void
    {
        config()->set('larasane.allow_code', ['basic', 'image', 'iframe']);

        $input = 'Go to <a href="http://myapp.com/useful.js">My Site</a>.';

        static::assertEquals(
            $input,
            Sanitizer::input($input)->enforceHttps(false)->sanitize()
        );

        $input = 'Check my <img src="http://myapp.com/image.png" alt="image" />';

        static::assertEquals(
            $input,
            Sanitizer::input($input)->enforceHttps(false)->sanitize()
        );

        $input = 'Look into <iframe src="http://myapp.com/useful"></iframe>';

        static::assertEquals(
            $input,
            Sanitizer::input($input)->enforceHttps(false)->sanitize()
        );
    }

    public function test_defaults_doesnt_allows_data_image(): void
    {
        config()->set('larasane.allow_code', ['image']);

        $input = 'Check my <img src="data:image/gif;base64,123456789" alt="image" />.';

        static::assertEquals(
            'Check my <img alt="image" />.',
            Sanitizer::input($input)->sanitize()
        );
    }

    public function test_overrides_data_image(): void
    {
        config()->set('larasane.allow_code', ['image']);

        $input = 'Check my <img src="data:image/gif;base64,123456789" alt="image" />.';

        static::assertEquals(
            $input,
            Sanitizer::input($input)->imageData(true)->sanitize()
        );
    }

    public function test_defaults_no_mailto_link(): void
    {
        $input = 'Send email to <a href="mailto:foo&#64;bar.com">me</a>.';

        static::assertEquals(
            'Send email to <a>me</a>.',
            Sanitizer::input($input)->sanitize()
        );
    }

    public function test_overrides_mailto_link(): void
    {
        $input = 'Send email to <a href="mailto:foo&#64;bar.com">me</a>.';

        static::assertEquals(
            $input,
            Sanitizer::input($input)->allowMailto()->sanitize()
        );
    }

    public function test_overrides_tags_attributes(): void
    {
        $input = 'This is <strong class="important">important</strong>.';

        static::assertEquals(
            'This is <strong>important</strong>.',
            Sanitizer::input($input)->sanitize()
        );

        static::assertEquals(
            $input,
            Sanitizer::input($input)->tagAttributes('strong', 'class')->sanitize()
        );
    }

    public function test_overrides_tag_config(): void
    {
        $input = 'Send email to <a href="mailto:foo&#64;bar.com">me</a>.';

        static::assertEquals(
            $input,
            Sanitizer::input($input)->tagConfig('a', 'allow_mailto', true)->sanitize()
        );
    }

    protected function tearDown(): void
    {
        @unlink($this->app->configPath('larasane.php'));

        parent::tearDown();
    }
}