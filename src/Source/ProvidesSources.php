<?php

namespace Dreitier\Alm\Inspecting\Source;

/**
 * Provide source information
 */
interface ProvidesSources
{
    /**
     * Find one or many source artifacts
     * @param ...$args
     * @return Artifacts
     */
    public function find(...$args): Artifacts;

    /**
     * Find source artifacts newer than the ['reference' => $version]
     * @param ...$args
     * @return Artifacts
     */
    public function findNewerThan(...$args): Artifacts;

    /**
     * Find source artifacts older than the ['reference' => $version]
     * @param ...$args
     * @return Artifacts
     */
    public function findOlderThan(...$args): Artifacts;
}
