<?php

namespace Dreitier\Alm\Inspecting\Kubernetes\Distribution\Rancher;

use Composer\Semver\VersionParser;
use Dreitier\Alm\Inspecting\Kubernetes\Distribution\Rancher\Release as RancherRelease;
use Dreitier\Alm\Inspecting\Versioning\Version;
use Dreitier\Alm\Inspecting\Versioning\VersionedCollection;

class RancherReleaseService
{
    public function findOfficialReleases(): VersionedCollection
    {
        $rancherReleaseDoc = file_get_contents("https://ranchermanager.docs.rancher.com/versions");
        $r = [];

        preg_match_all("/<tr><td><b>(?P<version>[a-z0-9\.]+)<\/b><\/td><td>(.*?)<\/td><td><a href=\"(?P<gh_tag_link>[^\"]*?)\".*?>.*?<\/a><\/td><td><a href=\"(?P<support_matrix>[^\"]*?)\"/", $rancherReleaseDoc, $match);

        foreach ($match[0] as $idx => $link) {
            $release = Version::of($match['version'][$idx]);
            $supportMatrix = $match['support_matrix'][$idx];
            $supportedMinorReleases = [];

            if ($release->comparable() >= (new VersionParser())->parseConstraints("2.7")) {
                $supportMatrixDoc = file_get_contents($supportMatrix);

                $hasDistroOverview = preg_match("/All other Distros<\/h\d+>.*?<tbody.*?>(?P<matrix>.*?)<\/tbody>/sim", $supportMatrixDoc, $matchB);

                if ($hasDistroOverview) {
                    $matrix = str_replace(array("\r", "\n", "\t", " "), '', $matchB['matrix']);
                    preg_match_all("/<tr>\s*<td.*?>\s*(?:<.*?>)?\s*(?P<target>[^\<]*?)\s*(?:<\/[\w\-]*?>)?\s*<\/td>\s*<td.*?>\s*(?:<.*?>)?\s*(?P<version>[^\<]*?)\s*(?:<\/[\w\-]+?>)?\s*<\/td>\s*<\/tr>/sim", $matrix, $matchC);

                    foreach ($matchC['target'] as $idx2 => $distribution) {
                        if (strtolower(trim($distribution)) == 'any') {
                            $supportedMinorReleases[] = Version::of($matchC['version'][$idx2]);
                        }
                    }
                }
            }

            $rancherRelease = new RancherRelease(
                version: $release,
                changelog: $match['gh_tag_link'][$idx],
                supportMatrix: $supportMatrix,
                supportedLatestKubernetesMinorReleases: VersionedCollection::of($supportedMinorReleases),
            );

            $r[] = $rancherRelease;
        }

        return VersionedCollection::of($r);
    }
}
