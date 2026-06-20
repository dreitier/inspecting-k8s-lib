<?php

namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Github;

use Dreitier\Alm\Inspecting\Helm\Domain\Model\Chart;
use Dreitier\Alm\Inspecting\Helm\Domain\Model\ChartName;
use Dreitier\Alm\Inspecting\Helm\Domain\Model\Releases;
use Dreitier\Alm\Inspecting\Helm\Infrastructure\ChartResolver;
use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Strategy\ChartReleasesFinderStrategy;
use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Strategy\ChartReleasesFindingContext;
use Dreitier\Alm\Inspecting\Versioning\VersionedCollection;

class ChartOnGithub implements ChartResolver
{
	public function __construct(
		public readonly ChartName                   $chartName,
		public readonly ChartResolvingOptions       $options,
		public readonly ChartReleasesFinderStrategy $chartVersionFinderStrategy,
	)
	{
	}

	private ?Chart $chart = null;

	/**
	 * Resolves the chart from GitHub. If it is already resolved, the cached instance is returned.
	 *
	 * @return Chart
	 */
	public function resolve(): Chart
	{
		if ($this->chart === null) {

			$ctx = new ChartReleasesFindingContext($this->options);
			$releaseReferences = $this->chartVersionFinderStrategy->find($ctx);
			$releases = [];

			foreach ($releaseReferences as $reference) {
				// instead of loading *all* Chart.yaml files at once, it is only loaded if requested.
				$releases[] = new ChartDetailsOnGithub(fn() => $ctx->loadChartYamlFromGithub($reference));
			}

			$this->chart = new Chart(
				$this->chartName,
				Releases::create(VersionedCollection::of($releases))
			);
		}
		return $this->chart;
	}
}