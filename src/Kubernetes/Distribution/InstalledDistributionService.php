<?php

namespace Dreitier\Alm\Inspecting\Kubernetes\Distribution;

use Dreitier\Alm\Inspecting\Kubernetes\ClientContext;
use Dreitier\Alm\Inspecting\Kubernetes\Distribution\Rancher\Release;
use Dreitier\Alm\Inspecting\Versioning\Version;
use Maclof\Kubernetes\Client;
use Maclof\Kubernetes\RepositoryRegistry;

class InstalledDistributionService
{
    public function __construct(public readonly ClientContext $client)
    {
    }

    public function detect(): array
    {
        return array_merge([], $this->detectRke1());
    }

    protected function detectRke1(): array
    {
        $r = [];

        $deployments = $this->client->deployments()->find();

        foreach ($deployments as $deployment) {
            $deployment = $deployment->toArray();
            $template = $deployment['spec']['template'];
            $cattleVersion = static::locateRancherCattleVersion($template);

            if ($cattleVersion) {
                $distribution = Installed::rke1(Release::of($cattleVersion));
                $r[] = $distribution;
            }
        }

        return $r;
    }

    private static function locateRancherCattleVersion(array $template): ?Version
    {
        if (isset($template['spec']['containers'])) {
            foreach ($template['spec']['containers'] as $containerTemplate) {
                if (preg_match('/^rancher\/rancher-agent:(.*)$/', $containerTemplate['image'], $r)) {
                    return Version::of($r[1]);
                }
            }
        }

        return null;
    }
}
