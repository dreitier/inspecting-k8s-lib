<?php

namespace Dreitier\Alm\Inspecting\Source;

use Dreitier\Alm\Inspecting\Versioning\Version;

class Artifact
{
    public function __construct(
        public readonly string   $type,
        public readonly ?string  $full = null,
        public readonly ?string  $scheme = null,
        public readonly ?string  $providerPath = null,
        public readonly ?string  $projectPath = null,
        public readonly ?string  $name = null,
        public readonly ?Version $version = null,
    )
    {
    }

    public function toArray(): array {
        return [
            'type' => $this->type,
        ];
    }

    public static function fromArray(array $array): Artifact {
        return new static(
            type: $array['type'],
        );
    }
}
