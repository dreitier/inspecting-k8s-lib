<?php
namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Strategy;

use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Factory\ReleaseReferencesBuilder;
use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Model\ChartReleases;

class ReleasesByLastCommitStrategy implements ChartReleasesFinderStrategy {
	public function find(ChartReleasesFindingContext $context): ChartReleases
	{
		$builder = new ReleaseReferencesBuilder();
		throw new \Exception("INVALID");
		return $builder->build();
	}
}
