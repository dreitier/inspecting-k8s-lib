<?php

namespace Dreitier\Alm\Inspecting\Versioning;

use Composer\Semver\Comparator;
use Traversable;


class VersionedCollection implements \ArrayAccess, \Countable, \IteratorAggregate
{
	private SortedBy $sortedBy = SortedBy::Unsorted;

	private function __construct(private(set) array $items)
	{
	}

	public static function of(array $items, bool $newestFirst = false): VersionedCollection
	{
		$r = new static($items);

		if ($newestFirst) {
			return $r->highestFirst();
		}

		return $r;
	}

	public function highestFirst(): VersionedCollection
	{
		if ($this->sortedBy != SortedBy::Newest) {
			usort($this->items, function ($a, $b) {
				return Version::toVersion($b)->compare(Version::toVersion($a));
			});
			$this->sortedBy = SortedBy::Newest;
		}

		return $this;
	}

	public function lowestFirst(): VersionedCollection
	{
		if ($this->sortedBy != SortedBy::Oldest) {
			usort($this->items, function ($a, $b) {
				return Version::toVersion($a)->compare(Version::toVersion($b));
			});
			$this->sortedBy = SortedBy::Oldest;
		}

		return $this;
	}


	public function first(): null|Version|Versionized
	{
		if (sizeof($this->items) > 0) {
			return $this->items[0];
		}

		return null;
	}

	public function last(): null|Version|Versionized
	{
		if (sizeof($this->items) > 0) {
			return $this->items[sizeof($this->items) - 1];
		}

		return null;
	}

	private function find(Version|Versionized $versionReference, callable $check): VersionedCollection
	{
		$versionReference = Version::toVersion($versionReference);

		$r = [];
		foreach ($this->items as $versionToCheck) {

			$doInclude = $check($versionReference->compare($versionToCheck));

			if ($doInclude) {
				$r[] = $versionToCheck;
			}
		}

		return VersionedCollection::of($r);

	}

	/**
	 * Returns lower (older, before) versions than the given one in a new VersionCollection instance.
	 *
	 * @param Version $versionReference
	 * @return VersionedCollection a NEW collection
	 */
	public function findLowerThan(Version|Versionized $versionReference): VersionedCollection
	{
		return $this->find($versionReference, fn(int $comparison) => $comparison > 0);
	}

	/**
	 * Returns higher (newer, after) or equal versions than the given one in a new VersionCollection instance.
	 *
	 * @param Version $versionReference
	 * @return VersionedCollection a NEW collection
	 */
	public function findHigherOrEqualThan(Version|Versionized $versionReference): VersionedCollection
	{
		return $this->find($versionReference, fn(int $comparison) => $comparison <= 0);
	}

	/**
	 * Returns higher (newer, after) versions than the given one in a new VersionCollection instance.
	 *
	 * @param Version $versionReference
	 * @return VersionedCollection a NEW collection
	 */
	public function findHigherThan(Version|Versionized $versionReference): VersionedCollection
	{
		return $this->find($versionReference, fn(int $comparison) => $comparison < 0);
	}

	public function offsetExists(mixed $offset): bool
	{
		return isset($this->items[$offset]);
	}

	public function offsetGet(mixed $offset): mixed
	{
		return isset($this->items[$offset]) ? $this->items[$offset] : null;
	}

	public function offsetSet(mixed $offset, mixed $value): void
	{
		throw new \Exception("Not allowed, immutable collection");
	}

	public function offsetUnset(mixed $offset): void
	{
		throw new \Exception("Not allowed, immutable collection");
	}

	public function count(): int
	{
		return sizeof($this->items);
	}

	public function getIterator(): Traversable
	{
		return new \ArrayIterator($this->items);
	}
}

enum SortedBy
{
	case Newest;
	case Oldest;
	case Unsorted;
}