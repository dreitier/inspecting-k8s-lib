<?php

namespace Dreitier\Alm\Inspecting\Helm;

use Composer\Semver\VersionParser;
use Composer\Semver\Comparator;
use Dreitier\Alm\Helm\Chart\ReleaseSummary as HelmChartRelease;
use Dreitier\Alm\Versioning\Version;

class Chart
{
    public function __construct(public readonly string $package)
    {
    }

    private mixed $outputModifier = null;

    private ?HelmChartRelease $latest = null;

    public function modifyOutput(callable $outputModifier): Chart
    {
        $this->outputModifier = $outputModifier;
        return $this;
    }

    public function getOutputModifier(): ?callable
    {
        return $this->outputModifier;
    }

    public function getLatestRelease(): HelmChartRelease
    {
        if (!$this->latest) {
            $this->latest = $this->findChart();
        }

        return $this->latest;
    }

    protected function findChart(?string $chartVersion = null): ?HelmChartRelease
    {
        $r = [];
        $url = 'https://artifacthub.io/api/v1/packages/helm/' . $this->package;

        if ($chartVersion) {
            $url .= '/' . $chartVersion;
        }

        $content = file_get_contents($url);
        $content = json_decode($content);

        return HelmChartRelease::of(
            $content->version,
            Application::of($content->app_version),
            $content
        );
    }

    protected function getAvailableVersions(): array
    {
        $current = $this->getLatestRelease();
        $r = [];

        foreach ($current->raw->available_versions as $available) {
            $r[] = $available->version;
        }

        return $r;
    }

    public function getOlderChartReleases(?string $semverNewerOrEqualTo = null): \Generator
    {
        $r = [];
        $availableVersions = $this->getAvailableVersions();
        $semverNewerOrEqualTo = $semverNewerOrEqualTo ? Version::comparableOf($semverNewerOrEqualTo) : null;

        foreach ($availableVersions as $availableVersion) {
            $semverAvailableVersion = Version::comparableOf($availableVersion);

            if ($semverNewerOrEqualTo
                && !Comparator::greaterThan($semverAvailableVersion, $semverNewerOrEqualTo)) {
                continue;
            }

            // use $availableVersion. This may be 5.41.9-distributed and differs from the $semverAvailableVersion
            $olderChart = $this->findChart($availableVersion);

            yield $olderChart;
            $r[] = $olderChart;
        }

        return $r;
    }
}
