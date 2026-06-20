<?php

namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Github;

use Dreitier\Alm\Inspecting\Helm\Domain\Model\ChartDetails;
use Dreitier\Alm\Inspecting\Versioning\Version;

class ChartDetailsOnGithub implements ChartDetails
{
	public function __construct(private readonly \Closure $resolver)
	{
	}

	private ?array $data = null;

	private function ensureLoaded()
	{
		if (null === $this->data) {
			$this->data = $this->resolver->call($this);
		}

		return $this->data;
	}

	public function getVersion(): Version
	{
		return Version::of($this->ensureLoaded()['version']);
	}

	public function getApplicationVersion(): ?Version
	{
		return Version::nullableOf($this->ensureLoaded()['appVersion']);
	}

	public function content(): array
	{
		return $this->ensureLoaded();
	}
}