<?php

namespace Dreitier\Alm\Inspecting\Kubernetes\Api;

use Dreitier\Alm\Inspecting\Source\Artifact;
use Dreitier\Alm\Inspecting\Source\ArtifactReference;
use Dreitier\Alm\Inspecting\Versioning\Version;

class DeployedHelmChart
{
	public function __construct(
		public readonly Version           $version,
		public readonly ArtifactReference $application,
		public readonly array             $raw = [],
	)
	{
	}

	public static function fromArray(array $args): DeployedHelmChart
	{
		return new DeployedHelmChart(
			version: Version::of($args['version']),
			application: ArtifactReference::fromArray($args['application']),
		);
	}

	public function toArray(): array
	{
		return [
			'version' => $this->version->raw,
			'application' => $this->application->toArray(),
		];
	}

	public static function fromKubernetesSpec(array $spec): DeployedHelmChart
	{
		$applicationReference = new ArtifactReference(
			name: $spec['name'],
			version: Version::of($spec['appVersion'] ?? $spec['version']),
			sourceArtifact: new Artifact('exe'),
		);

		return new DeployedHelmChart(
			version: Version::of($spec['version']),
			application: $applicationReference,
			raw: $spec
		);
	}
}