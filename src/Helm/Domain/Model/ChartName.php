<?php

namespace Dreitier\Alm\Inspecting\Helm\Domain\Model;

class ChartName
{
	public function __construct(
		public readonly string $value,
	)
	{
	}

	public static function of(string $value): ChartName
	{
		return new static($value);
	}

	public function __toString(): string
	{
		return $this->value;
	}
}
