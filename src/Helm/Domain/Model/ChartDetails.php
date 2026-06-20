<?php

namespace Dreitier\Alm\Inspecting\Helm\Domain\Model;

use Dreitier\Alm\Inspecting\Versioning\Version;
use Dreitier\Alm\Inspecting\Versioning\Versionized;

interface ChartDetails extends Versionized
{
	public function getApplicationVersion(): ?Version;
}