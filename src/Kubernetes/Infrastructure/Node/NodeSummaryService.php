<?php

namespace Dreitier\Alm\Inspecting\Kubernetes\Infrastructure\Node;

use Dreitier\Alm\Inspecting\Versioning\Version;
use Maclof\Kubernetes\Client;

class NodeSummaryService
{
    public function __construct(public readonly Client $client)
    {

    }

    public function summarize(): array
    {
        $r = [];
        $nodes = $this->client->nodes()->find();

        foreach ($nodes as $node) {
            $attr = $node->toArray();

            $addresses = $attr['status']['addresses'];
            $nodeInfo = $attr['status']['nodeInfo'];
            $images = $attr['status']['images'];

            $nodeSummary = new NodeSummary(
                internalIp: static::where($addresses, 'type', 'InternalIP', 'address'),
                externalIp: static::where($addresses, 'type', 'ExternalIP', 'address'),
                hostname: static::where($addresses, 'type', 'Hostname', 'address'),
                kubeletVersion: Version::of($nodeInfo['kubeletVersion']),
                containerRuntimeVersion: $nodeInfo['containerRuntimeVersion'],
                osImage: $nodeInfo['osImage'] ?? null,
                kernelVersion: $nodeInfo['kernelVersion'],
            );


            $r[] = $nodeSummary;
        }

        return $r;
    }

    private static function where($rows, $key, $value, $field)
    {
        foreach ($rows as $row) {
            if (isset($row[$key])) {
                if ($row[$key] == $value) {
                    return $row[$field];
                }
            }
        }

        return null;
    }
}
