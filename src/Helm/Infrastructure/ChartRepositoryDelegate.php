<?php

namespace Dreitier\Alm\Inspecting\Helm\Infrastructure;

use Dreitier\Alm\Inspecting\Helm\Domain\Model\Chart;
use Dreitier\Alm\Inspecting\Helm\Domain\Model\ChartName;
use Dreitier\Alm\Inspecting\Helm\Domain\Repository\ChartRepository;

class ChartRepositoryDelegate implements ChartRepository
{
	/**
	 * @var ChartResolver[]
	 */
	private array $resolvers = [];

	public function find(ChartName $chartName): Chart
	{
		throw_if(!isset($this->resolvers[$chartName->value]), new NoRepositoryRegisteredForChartException($chartName));

		return $this->resolvers[$chartName->value]->resolve();
	}

	public function add(ChartName $chartName, ChartResolver $resolver): void
	{
		$this->resolvers[$chartName->value] = $resolver;
	}
}
