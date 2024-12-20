<?php

namespace Dreitier\Alm\Inspecting\Helm\Github;

use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;
use Dreitier\Alm\Inspecting\Helm\Chart as HelmChart;
use Dreitier\Alm\Inspecting\Helm\Application;
use Dreitier\Alm\Inspecting\Helm\Chart\ReleaseSummary as HelmChartRelease;

class ChartSource extends HelmChart
{
    public function __construct(
        public readonly string  $package,
        public readonly Options $options
    )
    {
    }

    private ?array $olderReleases = null;

    protected function getAvailableVersions(): array
    {
        $this->resolveTagFromChartVersion('');
        return array_keys($this->chartToTag);
    }

    private ?array $chartToTag = null;

    private function resolveTagFromChartVersion($chartVersion): ?string
    {
        if (!$this->chartToTag) {
            $this->chartToTag = [];

            $options = array('http' => array('user_agent' => 'curl/7.81.0', 'accept' => '*/*'));
            $context = stream_context_create($options);

            try {
                $url = 'https://api.github.com/repos/' . $this->options->project . '/tags';
                $content = file_get_contents($url, false, $context);
                $tags = json_decode($content);

                foreach ($tags as $tagInfo) {
                    $tag = $tagInfo->name;
                    $helmChartRelease = $this->findChartByTagOrBranch($tag);

                    $this->chartToTag[$helmChartRelease->version] = $tag;
                }
            } catch (\Exception $e) {
                // swallow
            }
        }

        return $this->chartToTag[$chartVersion] ?? null;
    }


    protected function findChart(?string $chartVersion = null): ?HelmChartRelease
    {
        if ($chartVersion && !$this->options->gitTagEqualsChartVersion) {
            $chartVersion = $this->resolveTagFromChartVersion($chartVersion);
        }

        $tagOrBranch = $chartVersion ?? $this->options->branch;

        $r = $this->findChartByTagOrBranch($tagOrBranch);

        return $r;
    }

    protected function findChartByTagOrBranch(string $tagOrBranch): ?HelmChartRelease
    {
        $url = 'https://raw.githubusercontent.com/'
            . $this->options->project
            . '/'
            . $tagOrBranch
            . '/'
            . $this->package
            . '/Chart.yaml';

        $content = file_get_contents($url);

        // support YAML with multiple blocks
        if (preg_match_all('/^---([\s\S]*?)---/ui', $content, $matches)) {
            $content = $matches[1][0];
        }

        try {
            $value = Yaml::parse($content);

            return HelmChartRelease::of(
                $value['version'],
                Application::of($value['appVersion']),
                $value,
            );
        } catch (\Exception $exception) {
            throw new \Exception(sprintf('Failed to parse chart at "%s": Unable to parse the YAML string: %s', $url, $exception->getMessage()));
        }
    }
}
