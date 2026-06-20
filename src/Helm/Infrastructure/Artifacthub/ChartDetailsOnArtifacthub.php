<?php

namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Artifacthub;

use Dreitier\Alm\Inspecting\Helm\Domain\Model\ChartDetails;
use Dreitier\Alm\Inspecting\Versioning\Version;

class ChartDetailsOnArtifacthub implements ChartDetails
{
	public function __construct(
		public readonly Version $version,
		public readonly ?Version $applicationVersion,

		private readonly \Closure $resolver)
	{
	}

	private ?ArtifacthubReleaseResponse $data = null;

	private function ensureLoaded()
	{
		if (null === $this->data) {
			$this->data = $this->resolver->call($this);
		}

		return $this->data;
	}

	public function details(): ArtifacthubReleaseResponse
	{
		return $this->ensureLoaded();
	}

	public function getApplicationVersion(): ?Version
	{
		return $this->applicationVersion;
	}

	public function getVersion(): Version
	{
		return $this->version;
	}
}