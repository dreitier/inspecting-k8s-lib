<?php

namespace Dreitier\Alm\Inspecting\Helm\Infrastructure;

use Dreitier\Alm\Inspecting\Helm\Domain\Model\ChartName;

class NoRepositoryRegisteredForChartException extends \Exception
{
	public function __construct(public readonly ChartName $chartName)
	{
	}
}