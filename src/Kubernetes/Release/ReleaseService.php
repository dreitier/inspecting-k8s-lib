<?php

namespace Dreitier\Alm\Inspecting\Kubernetes\Release;

use Dreitier\Alm\Inspecting\Versioning\Version;
use Dreitier\Alm\Inspecting\Versioning\VersionedCollection;
use Dreitier\Alm\Inspecting\Kubernetes\Release as KubernetesRelease;

class ReleaseService
{
    public function findOfficialReleases(): VersionedCollection
    {
        $kubernetesReleaseDoc = file_get_contents('https://kubernetes.io/releases/');
        $kubernetesReleases = [];

        preg_match_all("/<a href=(.*)>([0-9\.]+)<\/a>/", $kubernetesReleaseDoc, $r);

        foreach ($r[0] as $idx => $link) {
            $kubernetesReleases[] = new KubernetesRelease(
                version: Version::of($r[2][$idx]),
                changelog: $r[1][$idx]
            );
        }

        return VersionedCollection::of($kubernetesReleases);
    }
}
