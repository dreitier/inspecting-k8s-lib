<?php

namespace Dreitier\Alm\Inspecting\Source;

use Dreitier\Alm\Versioning\Version;

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
}
