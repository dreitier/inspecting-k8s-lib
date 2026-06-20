<?php
namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Model;

class ProjectName {
	public function __construct(
		public readonly string $value,
	)
	{
	}

	public function __toString() : string {
		return $this->value;
	}
}
