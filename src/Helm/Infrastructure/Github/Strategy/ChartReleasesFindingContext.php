<?php

namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Strategy;

use Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\ChartResolvingOptions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Yaml\Yaml;

class ChartReleasesFindingContext
{
	public function __construct(
		public readonly ChartResolvingOptions $options,
		public readonly array   $args = []
	)
	{
	}

	public function buildChartPath(): string
	{
		$templates = $this->args['templated'] ?? [];
		$path = $this->options->buildChartPath();
		$knownVariableReferences = ['version'];

		foreach ($knownVariableReferences as $variableReference) {
			$matcher = '{{' . $variableReference . '}}';
			if (strpos($path, $matcher) !== false) {
				if (!isset($templates[$variableReference])) {
					throw new \Exception("Templated variable for '$variableReference' not previously resolved");
				}

				$path = str_replace($matcher, $templates[$variableReference], $path);
			}
		}

		return $path;
	}

	public function apiRequest(string $request): mixed
	{
		$options = array('http' => array('user_agent' => 'curl/7.81.0', 'accept' => '*/*'));
		$url = 'https://api.github.com/repos/' . $this->options->project . '/' . $request;

		Log::debug("Making GitHub API request: $url");

		$cacheKey = md5($url);
		$r = Cache::get($cacheKey, function() use ($url, $options) {
			$stream = stream_context_create($options);
			$content = file_get_contents($url, false, $stream);
			return $content;
		});

		Cache::put($cacheKey, $r);

		return json_decode($r);
	}

	public function loadChartYamlFromGithub(ReleaseReference $releaseReference): array
	{
		$url = 'https://raw.githubusercontent.com/'
			. $this->options->project
			. '/'
			. $releaseReference->gitReference->value
			. '/'
			. $releaseReference->pathToChartFile->value;

		Log::debug("Retrieving version $releaseReference from $url");

		$cacheKey = md5($url);
		$content = Cache::get($cacheKey, function() use ($url, $releaseReference) {
			return file_get_contents($url);
		});

		Cache::put($cacheKey, $content);

		// support YAML with multiple blocks
		if (preg_match_all('/^---([\s\S]*?)---/ui', $content, $matches)) {
			$content = $matches[1][0];
		}

		try {
			return Yaml::parse($content);
		} catch (\Exception $exception) {
			throw new \Exception(sprintf('Failed to parse chart at "%s": Unable to parse the YAML string: %s', $url, $exception->getMessage()));
		}
	}
}