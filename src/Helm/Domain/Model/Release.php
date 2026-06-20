<?php

namespace Dreitier\Alm\Inspecting\Helm\Domain\Model;

use Dreitier\Alm\Inspecting\Versioning\Version;

class Release
{
	public function __construct(
		public readonly ChartName     $name,
		public readonly Version       $version,
		public readonly ?ChartDetails $chartYaml = null,
	)
	{
	}
}

