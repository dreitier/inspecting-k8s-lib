<?php

namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Strategy;

use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Factory\ReleaseReferencesBuilder;
use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Model\GitReference;
use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Model\PathToChartFile;
use Dreitier\Alm\Inspecting\Versioning\Version;

/**
 * This is specific from something like rancher/charts
 */
class ReleasesByFolderStrategy implements ChartReleasesFinderStrategy
{
	public const MATCHER = '(?<version>.*)';

	public function __construct(
		public readonly ?string $pinpointToBranchOrTag = null,
		public readonly ?string $regexMatcher = self::MATCHER,
	)
	{
	}

	public function find(ChartReleasesFindingContext $context): ReleaseReferences
	{
		$builder = new ReleaseReferencesBuilder();

		try {
			$ref = $this->pinpointToBranch ?? '?ref=' . $this->pinpointToBranchOrTag;
			$request = 'contents/' . $context->options->baseDirectory . $ref;

			$directoriesWithTags = $context->apiRequest($request);

			foreach ($directoriesWithTags as $tag) {
				if (preg_match('/' . $this->regexMatcher . '/', $tag->name, $matches)) {
					if (!isset($matches['version'])) {
						throw new \Exception(self::MATCHER . " regex not set");
					}

					// get the current ref (branch/tag) from the url's file ?ref= GET parameter
					$urlToChart = parse_url($tag->url, PHP_URL_QUERY);
					if (!empty($urlToChart)) {
						parse_str($urlToChart, $componentsOfChartUrl);

						if ($componentsOfChartUrl['ref']) {
							$gitReference = $componentsOfChartUrl['ref'];
						}
 					}

					$chartFile = $context->options->baseDirectory . '/' . $tag->name . '/' . $context->options->chartFile;
					$builder->add(
						Version::of($matches['version']),
						new GitReference($gitReference),
						new PathToChartFile($chartFile)
					);
				}
			}
		} catch (\Exception $e) {
			throw new \Exception("Unable to find any Helm chart releases in subdirectories {base_directory=" . $context->options->baseDirectory . ',branch/tag=' . $this->pinpointToBranchOrTag . '}: ' . $e->getMessage());
			// swallow?
		}

		return $builder->build();
	}
}

