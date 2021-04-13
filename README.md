![
Clay Banks - Unslash (UL) #kBaf0DwBPbE](https://images.unsplash.com/photo-1584813470613-5b1c1cad3d69?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=crop&w=1280&h=400&q=80)

[![Latest Stable Version](https://poser.pugx.org/darkghosthunter/larasane/v/stable)](https://packagist.org/packages/darkghosthunter/larasane) [![License](https://poser.pugx.org/darkghosthunter/larasane/license)](https://packagist.org/packages/darkghosthunter/larasane) ![](https://img.shields.io/packagist/php-v/darkghosthunter/larasane.svg)  ![](https://github.com/DarkGhostHunter/Larasane/workflows/PHP%20Composer/badge.svg)  [![Coverage Status](https://coveralls.io/repos/github/DarkGhostHunter/Larasane/badge.svg?branch=master)](https://coveralls.io/github/DarkGhostHunter/Larasane?branch=master) [![Laravel Octane Compatible](https://img.shields.io/badge/Laravel%20Octane-Compatible-success?style=flat&logo=laravel)](https://github.com/laravel/octane)

# Larasane

Quickly sanitize text into safe-HTML using fluid methods.

## Requirements

* PHP 7.4, 8.0 or later.
* Laravel 7.x, 8.x or later.

## Usage

After you receive your HTML input you want to sanitize, use the `Sanitizer` facade to do it.

```php
<?php

use DarkGhostHunter\Larasane\Facades\Sanitizer;

$input = 'Trust <script src="https://malicio.us/code.js"></script> me!';

echo Sanitizer::input($input)->sanitize($input); // "Trust me!"
```

## Configuration

Larasane works out of the box to sanitize any tag that is not-basic, but you can configure the defaults by publishing the config file.

    php artisan vendor:publish --provider="DarkGhostHunter\Larasane\LarasaneServiceProvider" --tag="config"

You will receive the `config/larasane.php` file with the following contents.

```php
<?php

return [
    'max_length' => 1000,
    'allow_code' => [
        'basic',
    ],
    'security' => [
        'enforced_domains' => null,
        'enforce_https' => null,
        'image_data'    => false,
        'allow_mailto'  => false,
    ],
    'tags' => [
        'div' => 'class',
        'img' => ['src', 'alt', 'title', 'class'],
        'a' => ['class', 'target'],
        'ul' => 'class',
        'ol' => 'class',
        'li' => 'class',
    ]
];
```

### Max Length

```php
return [
    'max_length' => 1000,
];
```

Inputs to sanitize will be truncated at a max length. You can change this globally, or per sanitization.

```php
$sanitized = Sanitizer::for($input)->maxLength(200);
```

### Code allowed

```php
return [
    'allow_code' => [
        'basic', /* 'list', 'table', 'image', 'code', 'iframe', 'details', 'extra' */
    ],
];
```

The type tags to allow in an HTML input. These are grouped by the [name of the extension](https://github.com/tgalopin/html-sanitizer/blob/1.4.0/docs/1-getting-started.md#extensions), and only allows for basic HTML tags by default. You can override the list per-sanitization basis:   

```php
$sanitized = Sanitizer::for($input)->allowCode('basic', 'list', 'table');
```

If you need to accept custom tags, you should [create an extension](#adding-sanitization-extensions) to handle them. 

### Security

```php
return [
    'security' => [
        'enforce_hosts' => null,
        'enforce_https' => null,
        'image_data'    => false,
        'allow_mailto'  => false,
    ],
];
```

This groups some security features for handling links in `<a>`, `<img>` and `<iframe>` tags. These all can be overridden at runtime.

```php
$input = Sanitizer::for($input)
                  ->hosts('myapp.com')
                  ->enforceHttps(true)
                  ->imageData(true)
                  ->allowMailto(true);
```

#### `enforce_hosts`

You can set here a list of hosts to allow links, like `myapp.com`.

If `null`, no link protection will be enforced, so will allow links to point anywhere. If the list is empty, links on tags will appear empty.

#### `enforce_https`

Enforces HTTPS links, which will transform each link to `https` scheme. This is mostly required on `<iframe>`.

If `null`, it will be only enabled on production environments. 

#### `image_data`

Allow `<img>` to include image data in the source tag. This is sometimes desirable for small icons or images, as the image will be _embedded_ in the HTML code instead of being linked elsewhere.

```html
<img src="data:image/gif;base64,R0lGODlhEAAQAMQAAORHHOVSKudfOu..." />
```

#### `allow_mailto`

The `<a>` tags can have mail links. This simply allows or disallows them.

### Tags

```php
return [
    'tags' => [
        'div' => 'class',
        'img' => ['src', 'alt', 'title', 'class'],
        'a' => ['class', 'target'],
        'ul' => 'class',
        'ol' => 'class',
        'li' => 'class',
    ]
];
```

This is an array of allowed attributes for each tag that is sanitized. This may be useful for allow styling or disallow problematic attributes on the frontend. This can be overriden at runtime.

```php
$sanitized = Sanitizer::for($input)->tagAttributes('a', 'class', 'target');
```

## Adding Sanitization Extensions

You can create your own tag parsing extensions if you need more functionality apart from the ones included, like parsing custom tags. Once you [create your custom extension](https://github.com/tgalopin/html-sanitizer/blob/1.4.0/docs/2-creating-an-extension-to-allow-custom-tags.md), you can use `extend()` to add it to the Sanitizer builder in your `AppServiceProvider` or similar.

```php

use HtmlSanitizer\SanitizerBuilder;
use App\Sanitizer\MyExtension;

/**
 * Register the application services.
 *
 * @return void
 */
public function register()
{
    $this->app->extend(SanitizerBuilder::class, function (SanitizerBuilder $builder) {
        return $builder->registerExtension(new MyExtension());
    });
}
```

## License

This package is licenced by the [MIT License](LICENSE).