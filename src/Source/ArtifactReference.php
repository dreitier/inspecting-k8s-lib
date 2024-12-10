<?php

namespace Dreitier\Alm\Inspecting\Source;

use Dreitier\Alm\Inspecting\Versioning\Version;

/**
 * Reference to other source artifacts
 */
class ArtifactReference
{
    public function __construct(
        public readonly string   $name,
        public readonly Version  $version,
        public readonly Artifact $sourceArtifact,
    )
    {

    }

    public static function fromArray($args)
    {
        return new static(
            name: $args['name'],
            version: Version::of($args['version']),
            sourceArtifact: Artifact::fromArray($args['source'])
        );
    }

    public function toArray(): array
    {
        return [
            'version' => (string)$this->version,
            'name' => $this->name,
            'source' => $this->sourceArtifact->toArray(),
        ];
    }
}
