<?php
namespace Dreitier\Alm\Inspecting\Helm;

class Application {
	public function __construct(
		public readonly string $version,
	)
	{
	}
	
	public static function of(string $version): Application
	{
		return new static($version);
	}
	
	public function toArray(): array {
		return [
			'version' => $this->version,
		];
	}
	
	public static function fromArray(?array $args): ?Application {
		if (!$args || !isset($args['version'])) {
			return null;
		}
		
		return new static(
			version: $args['version'],
		);
	}

}