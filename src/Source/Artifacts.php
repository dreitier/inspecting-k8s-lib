<?php

namespace Dreitier\Alm\Inspecting\Source;

use Traversable;

/**
 * Container for source artifacts. It depends upon the ProvidesSources implementation what type of elements are inside this container.
 * They *may* be of type SourceArtifact but can also be something else.
 */
class Artifacts implements \IteratorAggregate, \Countable
{
    public function __construct(
        public readonly mixed $raw,
        public readonly array $sourceArtifacts
    )
    {
    }

    public function getIterator(): Traversable
    {
        return new \ArrayIterator($this->sourceArtifacts);
    }

    public function isEmpty()
    {
        return sizeof($this->sourceArtifacts) == 0;
    }

    public function first(): mixed
    {
        if (!$this->isEmpty()) {
            return $this->sourceArtifacts[0];
        }

        return null;
    }

    public function count(): int
    {
        return sizeof($this->sourceArtifacts);
    }
}
