<?php

namespace Dreitier\Alm\Inspecting\Source;

class Changelog
{
    public function __construct(
        public readonly mixed   $raw,
        public readonly ?string $url,
        public readonly ?string $content,
    )
    {
    }
}
