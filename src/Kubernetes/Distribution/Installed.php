<?php

namespace Dreitier\Alm\Inspecting\Kubernetes\Distribution;

use Dreitier\Alm\Inspecting\Versioning\Version;
use Dreitier\Alm\Inspecting\Versioning\Versionized;

class Installed implements Versionized
{
    public function __construct(
        public readonly Type        $type,
        public readonly Versionized $distribution
    )
    {
    }

    public static function rke1(Versionized $version)
    {
        return new static(Type::RANCHER_RKE1, $version);
    }

    public static function rke2(Versionized $version)
    {
        return new static(Type::RANCHER_RKE2, $version);
    }

    public function getVersion(): Version
    {
        return $this->distribution->getVersion();
    }
}
