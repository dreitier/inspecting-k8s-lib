<?php

namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Artifacthub;

use Dreitier\Alm\Inspecting\Helm\Domain\Model\ChartName;
use Dreitier\Alm\Inspecting\Versioning\Version;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class FindChartReleaseOnArtifacthubContext
{
	public function __construct(public readonly ChartName $chartName)
	{
	}

	/**
	 * @return ChartDetailsOnArtifacthub[]
	 */
	public function allReleaseVersions(): array
	{
		$content = $this->find();
		$r = [];

		foreach ($content->raw['available_versions'] as $versionField) {
			$chartVersion = Version::of($versionField['version']);
			$appVersion = Version::nullableOf($versionField['app_version'] ?? null);


			$r[] = new ChartDetailsOnArtifacthub(
				$chartVersion,
				$appVersion,
				fn() => $this->find($chartVersion)
			);
		}

		return $r;
	}

	public function find(?Version $version = null): ArtifacthubReleaseResponse
	{
		$url = 'https://artifacthub.io/api/v1/packages/helm/' . $this->chartName;

		if ($version) {
			$url .= '/' . $version;
		}

		Log::debug("Making Artifacthub API request: $url");

		$cacheKey = md5($url);
		$r = Cache::get($cacheKey, function () use ($url) {
			$stream = stream_context_create([]);
			$content = file_get_contents($url, false, $stream);
			return $content;
		});

		Cache::put($cacheKey, $r);

		$decoded = json_decode($r, true);
		return ArtifacthubReleaseResponse::create($decoded);
	}
}