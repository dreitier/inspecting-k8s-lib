<?php

namespace Dreitier\Alm\Inspecting\Helm\Infrastructure\Github\Strategy;

use Traversable;

class ReleaseReferences implements \IteratorAggregate
{

	public function __construct(private readonly array $items) {}

	public function getIterator(): Traversable
	{
		return new \ArrayIterator($this->items);
	}
}