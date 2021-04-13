<?php

namespace DarkGhostHunter\Larasane;

use HtmlSanitizer\SanitizerBuilder;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Arr;
use Illuminate\Support\Stringable;

class PendingSanitization
{
    use BuildsSanitizer;

    /**
     * The Sanitizer builder
     *
     * @var \HtmlSanitizer\SanitizerBuilder
     */
    protected SanitizerBuilder $builder;

    /**
     * Application config.
     *
     * @var \Illuminate\Contracts\Config\Repository
     */
    protected Repository $config;

    /**
     * Length to truncate the input.
     *
     * @var int|null
     */
    protected ?int $maxLength = null;

    /**
     * Extensions to invoke for the sanitizer.
     *
     * @var array|null
     */
    protected ?array $extensions = null;

    /**
     * Hosts to allow on links, images and iframes.
     *
     * @var array|string[]
     */
    protected ?array $hosts = null;

    /**
     * If all links should be done to HTTPS origins.
     *
     * @var bool|null
     */
    protected ?bool $https = null;
    /**
     * If data image should be allowed on images.
     *
     * @var bool|null
     */
    protected ?bool $imageData = null;

    /**
     * If "mailto:" links should be allowed.
     *
     * @var bool|null
     */
    protected ?bool $mailto = null;

    /**
     * List of properties allowed for each tag.
     *
     * @var array<string[]>|string[]|null
     */
    protected ?array $tagAttributes = null;

    /**
     * Custom config to pass to a tag.
     *
     * @var array|null
     */
    protected ?array $tagConfig = null;

    /**
     * The input to sanitize.
     *
     * @var \Illuminate\Support\Stringable|string|\Stringable
     */
    protected $input = '';

    /**
     * PendingSanitization constructor.
     *
     * @param  \HtmlSanitizer\SanitizerBuilder  $builder
     * @param  \Illuminate\Contracts\Config\Repository  $config
     */
    public function __construct(SanitizerBuilder $builder, Repository $config)
    {
        $this->builder = $builder;
        $this->config = $config;
    }

    /**
     * Override the length to truncate the input.
     *
     * @param  int  $int
     *
     * @return \DarkGhostHunter\Larasane\PendingSanitization
     */
    public function maxLength(int $int): PendingSanitization
    {
        $this->maxLength = $int;

        return $this;
    }

    /**
     * Override the list of code extensions to allow in the HTML.
     *
     * @param  string  ...$extensions
     *
     * @return $this
     */
    public function allowCode(string ...$extensions): PendingSanitization
    {
        $this->extensions = $extensions;

        return $this;
    }

    /**
     * Override the list of accepted hosts for links.
     *
     * @param  string  ...$hosts
     *
     * @return \DarkGhostHunter\Larasane\PendingSanitization
     */
    public function hosts(string ...$hosts): PendingSanitization
    {
        $this->hosts = $hosts;

        return $this;
    }

    /**
     * Allow links to point anywhere.
     *
     * @return $this
     */
    public function anyHost(): PendingSanitization
    {
        $this->hosts = null;

        return $this;
    }

    /**
     * Disallows any link.
     *
     * @return $this
     */
    public function disallowLinks(): PendingSanitization
    {
        $this->hosts = [];

        return $this;
    }

    /**
     * Override the HTTPS link enforcement.
     *
     * @param  bool  $enforce
     *
     * @return $this
     */
    public function enforceHttps(bool $enforce = true): PendingSanitization
    {
        $this->https = $enforce;

        return $this;
    }

    /**
     * Allows or disallows image data in image.
     *
     * @param  bool  $allow
     *
     * @return $this
     */
    public function imageData(bool $allow = true): PendingSanitization
    {
        $this->imageData = $allow;

        return $this;
    }

    /**
     * Allows or disallows "mailto:" links.
     *
     * @param  bool  $allow
     *
     * @return \DarkGhostHunter\Larasane\PendingSanitization
     */
    public function allowMailto(bool $allow = true): PendingSanitization
    {
        $this->mailto = $allow;

        return $this;
    }

    /**
     * Override a list of allowed attributes for a given tag.
     *
     * @param  string  $tag
     * @param  string  ...$allowedAttributes
     *
     * @return \DarkGhostHunter\Larasane\PendingSanitization
     */
    public function tagAttributes(string $tag, string ...$allowedAttributes): PendingSanitization
    {
        $this->tagAttributes[$tag] = $allowedAttributes;

        return $this;
    }

    /**
     * Sets a custom config in the tag.
     *
     * @param  string  $tag
     * @param  string  $key
     * @param  string[]|array  $value
     *
     * @return \DarkGhostHunter\Larasane\PendingSanitization
     */
    public function tagConfig(string $tag, string $key, $value): PendingSanitization
    {
        Arr::set($this->tagConfig, "$tag.$key", $value);

        return $this;
    }

    /**
     * @param  string|\Stringable|\Illuminate\Support\Stringable  $input
     *
     * @return \DarkGhostHunter\Larasane\PendingSanitization
     */
    public function input($input): PendingSanitization
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Returns the sanitized input.
     *
     * @return \Illuminate\Support\Stringable
     */
    public function sanitize(): Stringable
    {
        return new Stringable($this->build()->sanitize($this->input));
    }
}