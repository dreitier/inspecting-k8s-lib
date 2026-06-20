<?php

namespace Dreitier\Alm\Inspecting\Kubernetes\Api;

use Dreitier\Alm\Inspecting\Helm\Chart\ReleaseDeprecated;
use Dreitier\Alm\Inspecting\Helm\Chart\ReleaseSummary;
use Dreitier\Alm\Inspecting\Helm\Domain\Model\Release;
use Dreitier\Alm\Inspecting\Helm\Infrastructure\Artifacthub\ArtifacthubReleaseResponse;
use Dreitier\Alm\Inspecting\Source\ArtifactReference;
use Dreitier\Alm\Inspecting\Versioning\Version;

class DeployedApplication
{
	public function __construct(
		public readonly string             $ns,
		public readonly string             $deploymentName,
		public readonly ?DeployedHelmChart $deployedHelmChart,
		public readonly array              $sources)
	{
	}

	public function __toString(): string
	{
		return $this->ns
			. ":"
			. $this->deploymentName
			. " (Chart: "
			. $this->deployedHelmChart->version
			. ", App: " . $this->deployedHelmChart->application->version
			. ")";
	}

	public function toArray(): array
	{
		return [
			'namespace' => $this->ns,
			'deployment' => $this->deploymentName,
			'deployedHelmChart' => $this->deployedHelmChart->toArray(),
			'sources' => $this->sources,
		];
	}

	public static function fromArray(array $args): DeployedApplication {
		return new static(
			ns: $args['namespace'],
			deploymentName: $args['deployment'],
			deployedHelmChart: DeployedHelmChart::fromArray($args['deployedHelmChart']),
			sources: $args['sources'] ?? []
		);
	}
}

