<?php
namespace Dreitier\Alm\Inspecting\Kubernetes\Api;

use Dreitier\Alm\Inspecting\Helm\Chart\ReleaseSummary;

class DeployedApplication {
	public function __construct(
		public readonly string         $ns,
		public readonly string         $deploymentName,
		public readonly ReleaseSummary $helmChartRelease,
		public readonly array          $sources)
	{
	}

	public function __toString(): string {
		return $this->ns
			. ":"
			. $this->deploymentName
			. " (Chart: "
				. $this->helmChartRelease->version
				. ", App: " . $this->helmChartRelease->application?->version
			. ")";
	}

	public function toArray(): array {
		return [
			'namespace' => $this->ns,
			'deployment' => $this->deploymentName,
			'helmChartRelease' => $this->helmChartRelease->toArray(),
			'sources' => $this->sources,
		];
	}

	public static function fromArray(array $args): DeployedApplication {
		return new static(
			ns: $args['namespace'],
			deploymentName: $args['deployment'],
			helmChartRelease: ReleaseSummary::fromArray($args['helmChartRelease']),
			sources: $args['sources'] ?? []
		);
	}
}
