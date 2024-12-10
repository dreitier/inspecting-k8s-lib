<?php

namespace Dreitier\Alm\Inspecting\Kubernetes;

use Maclof\Kubernetes\Client;

class ClientContext
{
    public function __construct(
        public readonly string $clusterName,
        public readonly string $endpoint,
        public readonly Client $client,
    )
    {
    }
}
