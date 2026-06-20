<?php

namespace Dreitier\Alm\Inspecting\Helm\Infrastructure;

use Dreitier\Alm\Inspecting\Helm\Domain\Model\Chart;
use Dreitier\Alm\Inspecting\Helm\Domain\Model\ChartName;
use Dreitier\Alm\Inspecting\Helm\Domain\Repository\ChartRepository;

interface ChartResolver
{
	public function resolve(): Chart;
}
