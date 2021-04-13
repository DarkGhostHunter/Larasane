<?php

namespace DarkGhostHunter\Larasane;

use HtmlSanitizer\SanitizerInterface;
use Illuminate\Support\Arr;

trait BuildsSanitizer
{
    /**
     * Returns a prepared Sanitizer instance.
     *
     * @return \HtmlSanitizer\SanitizerInterface
     */
    protected function build(): SanitizerInterface
    {
        return $this->builder->build(
            [
                'max_input_length' => $this->parseMaxLength(),
                'extensions' => $this->parseExtensions(),
                'tags' => $this->parseTags(),
            ]
        );
    }

    /**
     * Parses the max length of the input.
     *
     * @return mixed
     */
    protected function parseMaxLength()
    {
        return $this->maxLength = $this->maxLength ?? $this->config->get('larasane.max_length');
    }

    /**
     * Parse the extensions to use with the Sanitizer.
     *
     * @return array
     */
    protected function parseExtensions(): array
    {
        return $this->extensions = $this->extensions ?? $this->config->get('larasane.allow_code');
    }

    /**
     * Parse the tags properties and config.
     *
     * @return array
     */
    protected function parseTags(): array
    {
        $tagsConfig = $this->getDefaultTagsConfig();

        foreach ($this->tagAttributes ?? [] as $tag => $attribute) {
            Arr::set($tagsConfig, "$tag.allowed_attributes", Arr::wrap($attribute));
        }

        foreach ($this->tagConfig ?? [] as $tag => $config) {
            Arr::set($tagsConfig, $tag, $config);
        }

        return $tagsConfig;
    }

    /**
     * Returns the default tag config array.
     *
     * @return array
     */
    protected function getDefaultTagsConfig(): array
    {
        $array = [
            'a' => [
                'force_https' => $forceHttps = $this->https ?? $this->parseEnforceHttps(),
                'allow_mailto' => $this->mailto ?? $this->config->get('larasane.security.allow_mailto', false),
            ],
            'iframe' => [
                'force_https' => $forceHttps,
            ],
            'img' => [
                'force_https' => $forceHttps,
                'allow_data_uri' => $this->imageData ?? $this->config->get('larasane.security.image_data', false),
            ]
        ];

        $allowedHosts = $this->hosts ?? $this->config->get('larasane.security.enforce_hosts');

        if ($allowedHosts !== null) {
            $array['a']['allowed_hosts'] = $allowedHosts;
            $array['iframe']['allowed_hosts'] = $allowedHosts;
            $array['img']['allowed_hosts'] = $allowedHosts;
        }

        return $array;
    }

    /**
     * Check if should enforce HTTPS on links.
     *
     * @return bool
     */
    protected function parseEnforceHttps(): bool
    {
        return $this->config->get('larasane.security.enforce_https') ?? app()->isProduction();
    }
}