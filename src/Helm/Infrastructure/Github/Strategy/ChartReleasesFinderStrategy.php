<?php
namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Strategy;

interface ChartReleasesFinderStrategy {
	public function find(ChartReleasesFindingContext $context): ReleaseReferences;
}