<?php

namespace Dreitier\Alm\Inspecting\Helm\Domain\Model;

use Dreitier\Alm\Inspecting\Versioning\Version;
use Dreitier\Alm\Inspecting\Versioning\VersionedCollection;
use Dreitier\Alm\Inspecting\Versioning\Versionized;
use Traversable;

class Releases implements \IteratorAggregate
{
	public static function create(VersionedCollection $collection): static
	{
		return new Releases($collection);
	}

	private function __construct(private(set) VersionedCollection $collection)
	{
		/** @var $items Versionized[] */
		if (sizeof($collection) > 0) {
			if (!(($collection[0] instanceof Version) || ($collection[0] instanceof Versionized))) {
				throw new \InvalidArgumentException("Element of type " . get_class($collection[0]) . " does not implement Version or Versionized when creating new Releases");
			}
		}

		$this->collection = $collection->highestFirst();
	}

	public function getIterator(): Traversable
	{
		return $this->collection->getIterator();
	}
}
