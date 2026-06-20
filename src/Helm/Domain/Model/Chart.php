<?php
namespace Dreitier\Alm\Inspecting\Helm\Domain\Model;

use Dreitier\Alm\Inspecting\Versioning\Version;

class Chart {
	public function __construct(
		public readonly ChartName $name,
		public readonly Releases $releases,
	)
	{
	}

	public function newestRelease(): ?Release {
		/** @var ChartDetails $chartYaml */
		$chartYaml = $this->releases->collection->first();

		if ($chartYaml === null) {
			return null;
		}

		return new Release($this->name, $chartYaml->getVersion(), $chartYaml);
	}

	public function releasesWithLowerVersionThan(Version $version): Releases {
		return Releases::create($this->releases->collection->findLowerThan($version));
	}

	public function releasesWithHigherVersionThan(Version $version): Releases {
		return Releases::create($this->releases->collection->findHigherThan($version));
	}
}
