<?php

namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Artifacthub;

use Dreitier\Alm\Inspecting\Helm\Domain\Model\Chart;
use Dreitier\Alm\Inspecting\Helm\Domain\Model\ChartName;
use Dreitier\Alm\Inspecting\Helm\Domain\Model\Releases;
use Dreitier\Alm\Inspecting\Helm\Infrastructure\ChartResolver;
use Dreitier\Alm\Inspecting\Versioning\Version;
use Dreitier\Alm\Inspecting\Versioning\VersionedCollection;

class ChartOnArtifacthub implements ChartResolver
{
	public function __construct(
		public readonly ChartName $chartName,
	)
	{
	}

	private ?Chart $chart = null;

	/**
	 * Resolves the chart from Artifacthub
	 *
	 * @return Chart
	 */
	public function resolve(): Chart
	{
		if ($this->chart === null) {

			$ctx = new FindChartReleaseOnArtifacthubContext($this->chartName);
			$releases = $ctx->allReleaseVersions();

			$this->chart = new Chart(
				$this->chartName,
				Releases::create(VersionedCollection::of($releases))
			);

		}

		return $this->chart;
	}
}

