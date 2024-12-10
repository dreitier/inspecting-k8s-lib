<?php

namespace Dreitier\Alm\Inspecting\Kubernetes\Infrastructure\Node;

use Dreitier\Alm\Inspecting\Versioning\Version;
use Dreitier\Alm\Inspecting\Kubernetes\Distribution\Rancher\Release as RancherRelease;

class NodeSummary
{
    public function __construct(
        public readonly string  $internalIp,
        public readonly ?string $externalIp,
        public readonly string  $hostname,
        public readonly Version $kubeletVersion,
        public readonly string  $containerRuntimeVersion,
        public readonly string  $osImage,
        public readonly ?string $kernelVersion,
    )
    {
    }
}
