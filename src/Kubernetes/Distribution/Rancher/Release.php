<?php

namespace Dreitier\Alm\Inspecting\Kubernetes\Distribution\Rancher;

use Dreitier\Alm\Inspecting\Versioning\HasVersion;
use Dreitier\Alm\Inspecting\Versioning\Version;
use Dreitier\Alm\Inspecting\Versioning\VersionedCollection;
use Dreitier\Alm\Inspecting\Versioning\Versionized;

class Release implements Versionized
{
    use HasVersion;

    public function __construct(
        public readonly Version             $version,
        public readonly VersionedCollection $supportedLatestKubernetesMinorReleases,
        public readonly ?string             $supportMatrix = null,
        public readonly ?string             $changelog = null,
    )
    {
    }

    public static function of(Version $version)
    {
        return new static(
            version: $version,
            supportedLatestKubernetesMinorReleases: VersionedCollection::empty()
        );
    }
}
