<?php

namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Strategy;

use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Factory\ReleaseReferencesBuilder;
use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Model\GitReference;
use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Model\PathToChartFile;
use Dreitier\Alm\Inspecting\Versioning\Version;

class ReleasesByGitTag implements ChartReleasesFinderStrategy
{
	public const MATCHER = '(?<version>.*)';

	public function __construct(
		public readonly string $regexMatcher = self::MATCHER,
	)
	{
	}

	public function find(ChartReleasesFindingContext $context): ReleaseReferences
	{
		$builder = new ReleaseReferencesBuilder();

		try {
			$tags = $context->apiRequest('tags');

			foreach ($tags as $tagInfo) {
				$gitTag = $tagInfo->name;

				if (preg_match('/' . $this->regexMatcher . '/', $gitTag, $matches)) {
					if (!isset($matches['version'])) {
						throw new \Exception(self::MATCHER . " regex not set");
					}

					$helmChartRelease = $matches['version'];
					$builder->add(
						Version::of($helmChartRelease),
						new GitReference($gitTag),
						new PathToChartFile($context->buildChartPath())
					);
				}
			}
		} catch (\Exception $e) {
			die($e->getMessage());
		}

		return $builder->build();
	}
}