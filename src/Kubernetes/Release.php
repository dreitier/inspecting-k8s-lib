<?php

namespace Dreitier\Alm\Kubernetes;

use Dreitier\Alm\Versioning\HasVersion;
use Dreitier\Alm\Versioning\Version;
use Dreitier\Alm\Versioning\VersionedCollection;
use Dreitier\Alm\Versioning\Versionized;

class Release implements Versionized
{
    use HasVersion;

    public function __construct(
        public readonly Version $version,
        public readonly string  $changelog
    )
    {
    }
}
