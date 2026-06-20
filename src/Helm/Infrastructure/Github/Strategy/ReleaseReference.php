<?php

namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Strategy;

use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Model\GitReference;
use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Model\PathToChartFile;
use Dreitier\Alm\Inspecting\Versioning\Version;
use Dreitier\Alm\Inspecting\Versioning\VersionDeprecated;
use Dreitier\Alm\Inspecting\Versioning\Versionized;

class ReleaseReference implements Versionized
{
	public function __construct(
		public readonly Version         $version,
		public readonly GitReference    $gitReference,
		public readonly PathToChartFile $pathToChartFile)
	{
	}

	public function getVersion(): Version
	{
		return $this->version;
	}

	public function __toString(): string
	{
		return $this->version->__toString();
	}
}