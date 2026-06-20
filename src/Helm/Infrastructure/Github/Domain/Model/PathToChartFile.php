<?php
namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Model;

class PathToChartFile {
	public function __construct(
		public readonly string $value,
	)
	{
	}
}
