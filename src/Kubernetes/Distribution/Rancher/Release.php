<?php

namespace Dreitier\Alm\Inspecting\Kubernetes\Distribution\Rancher;

use Dreitier\Alm\Inspecting\Versioning\HasVersion;
use Dreitier\Alm\Inspecting\Versioning\VersionDeprecated;
use Dreitier\Alm\Inspecting\Versioning\VersionedCollectionDeprecated;
use Dreitier\Alm\Inspecting\Versioning\Versionized;

class Release implements Versionized
{
    use HasVersion;

    public function __construct(
        public readonly VersionDeprecated             $version,
        public readonly VersionedCollectionDeprecated $supportedLatestKubernetesMinorReleases,
        public readonly ?string                       $supportMatrix = null,
        public readonly ?string                       $changelog = null,
    )
    {
    }

    public static function of(VersionDeprecated $version)
    {
        return new static(
            version: $version,
            supportedLatestKubernetesMinorReleases: VersionedCollectionDeprecated::empty()
        );
    }
}
