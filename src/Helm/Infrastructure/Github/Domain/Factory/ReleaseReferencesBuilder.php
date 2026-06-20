<?php

namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Factory;

use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Model\GitReference;
use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Model\PathToChartFile;
use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Strategy\ReleaseReference;
use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Strategy\ReleaseReferences;
use Dreitier\Alm\Inspecting\Versioning\Version;

class ReleaseReferencesBuilder
{

	private array $items = [];

	public function add(Version $version, GitReference $reference, PathToChartFile $pathToChartFile): ReleaseReferencesBuilder
	{
		$this->items[] = new ReleaseReference($version, $reference, $pathToChartFile);
		return $this;
	}

	public function build(): ReleaseReferences
	{
		return new ReleaseReferences($this->items);
	}
}
