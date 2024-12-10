<?php

namespace Dreitier\Alm\Inspecting\Versioning;

use Composer\Semver\VersionParser;

interface Versionized
{
    public function getVersion(): Version;
}
