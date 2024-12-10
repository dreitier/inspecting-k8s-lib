<?php
namespace Dreitier\Alm\Inspecting\Helm\Github;

class Options {
	public function __construct(
		public readonly string $project,
		public readonly string $branch,
		public readonly string $tagPrefix,
		public readonly bool $gitTagEqualsChartVersion,
	)
	{
	}
}