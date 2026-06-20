<?php
namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Github;

use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Model\ProjectName;

class ChartResolvingOptions {
	public function __construct(
		public readonly ProjectName $project,
		public readonly ?string $baseDirectory = null,
		public readonly string $defaultBranch = 'main',
		public readonly string $chartFile = 'Chart.yaml',
	)
	{
	}

	public function buildChartPath(): string {
		$prefix = '';

		if ($this->baseDirectory) {
			$prefix = $this->baseDirectory . '/';
		}

		return $prefix. $this->chartFile;
	}
}
