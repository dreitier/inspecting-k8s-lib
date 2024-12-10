<?php
namespace Dreitier\Alm\Inspecting\Helm\Chart;

use Dreitier\Alm\Inspecting\Helm\Application;

/**
 * @deprecated
 */
class ReleaseSummary {
	public function __construct(
		public readonly string $version,
		public readonly ?Application $application,
		public readonly mixed $raw = null,
	)
	{
	}

	public static function of(string $version, ?Application $application, mixed $raw): ReleaseSummary
	{
		return new static($version, $application, $raw);
	}

	public function toArray(): array {
		return [
			'version' => $this->version,
			'application' => $this->application?->toArray() ?? null
		];
	}

	public static function fromArray(array $args): ReleaseSummary {
		return new static(
			version: $args['version'],
			application: Application::fromArray($args['application'] ?? null)
		);
	}
}
