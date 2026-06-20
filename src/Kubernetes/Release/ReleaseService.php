<?php

namespace Dreitier\Alm\Inspecting\Kubernetes\Release;

use Dreitier\Alm\Inspecting\Versioning\VersionDeprecated;
use Dreitier\Alm\Inspecting\Versioning\VersionedCollectionDeprecated;
use Dreitier\Alm\Inspecting\Kubernetes\Release as KubernetesRelease;

class ReleaseService
{
    public function findOfficialReleases(): VersionedCollectionDeprecated
    {
        $kubernetesReleaseDoc = file_get_contents('https://kubernetes.io/releases/');
        $kubernetesReleases = [];

        preg_match_all("/<a href=(.*)>([0-9\.]+)<\/a>/", $kubernetesReleaseDoc, $r);

        foreach ($r[0] as $idx => $link) {
            $kubernetesReleases[] = new KubernetesRelease(
                version: VersionDeprecated::of($r[2][$idx]),
                changelog: $r[1][$idx]
            );
        }

        return VersionedCollectionDeprecated::of($kubernetesReleases);
    }
}
