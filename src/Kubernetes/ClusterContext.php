<?php

namespace Dreitier\Alm\Inspecting\Kubernetes;

/**
 * @deprecated
 */
class ClusterContext
{
    public function __construct(
        public readonly string $clusterName,
        public readonly string $endpoint,
        public readonly string $token)
    {

    }
}
