<?php

namespace Dreitier\Alm\Inspecting\Versioning;

use Composer\Semver\Comparator;


class VersionedCollection
{
    public function __construct(public readonly array $collection)
    {
    }

    public static function empty(): VersionedCollection
    {
        return static::of([]);
    }

    public static function of(array $versioned): VersionedCollection
    {
        return new static($versioned);
    }

    public function newestFirst(): VersionedCollection
    {
        $r = $this->collection;

        usort($r, function ($a, $b) {
            return Comparator::lessThan($a->getVersion(), $b->getVersion());
        });

        return VersionedCollection::of($r);
    }

    private function toVersion(Version|Versionized $version): Version
    {
        if ($version instanceof Version) {
            return $version;
        }

        return $version->getVersion();
    }

    public function find(Version|Versionized $version): ?Versionized
    {
        foreach ($this->collection as $toCheck) {
            if (Comparator::equalTo($this->toVersion($toCheck), $this->toVersion($version))) {
                return $toCheck;
            }
        }

        return null;
    }

    public function first(): null|Version|Versionized
    {
        if (sizeof($this->collection) > 0) {
            return $this->collection[0];
        }

        return null;
    }

    public function oldestFirst(): VersionedCollection
    {
        return VersionedCollection::of(array_reverse($this->newestFirst()->collection));
    }

    public function findBeforeThan(Version|Versionized $version): VersionedCollection
    {
        $r = [];
        foreach ($this->collection as $versionToCheck) {
            if (Comparator::lessThan($this->toVersion($versionToCheck), $this->toVersion($version))) {
                $r[] = $versionToCheck;
            }
        }

        return VersionedCollection::of($r);
    }

    public function findNewerOrEqualThan(Version|Versionized $version): VersionedCollection
    {
        $r = [];
        foreach ($this->collection as $versionToCheck) {
            if (Comparator::greaterThanOrEqualTo($this->toVersion($versionToCheck), $this->toVersion($version))) {
                $r[] = $versionToCheck;
            }
        }


        return VersionedCollection::of($r);
    }

    public function findNewerThan(Version|Versionized $version): VersionedCollection
    {
        $r = [];
        foreach ($this->collection as $versionToCheck) {
            if (Comparator::greaterThan($this->toVersion($versionToCheck), $this->toVersion($version))) {
                $r[] = $versionToCheck;
            }
        }


        return VersionedCollection::of($r);
    }
}
