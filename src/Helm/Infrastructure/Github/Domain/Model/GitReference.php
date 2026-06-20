<?php
namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Domain\Model;

class GitReference {
	public function __construct(
		public readonly string $value,
	)
	{
	}
}
