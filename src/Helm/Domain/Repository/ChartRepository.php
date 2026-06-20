<?php
namespace Dreitier\Alm\Inspecting\Helm\Domain\Repository;

use Dreitier\Alm\Inspecting\Helm\Domain\Model\Chart;
use Dreitier\Alm\Inspecting\Helm\Domain\Model\ChartName;

interface ChartRepository {
	public function find(ChartName $chartName): Chart;
}
