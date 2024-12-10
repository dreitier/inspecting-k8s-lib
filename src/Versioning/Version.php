<?php

namespace Dreitier\Alm\Inspecting\Versioning;

use Composer\Semver\Constraint\ConstraintInterface;
use Composer\Semver\VersionParser;

class Version
{
    public function __construct(
        public readonly string $raw,
        public readonly string $version,
        public readonly ?string $flavor = null,
    )
    {
    }

    public static function nullableOf(?string $version): ?Version
    {
        if ($version) {
            return static::of($version);
        }

        return null;
    }

    public static function of(string $version): Version
    {
        $sanitized = static::sanitize($version);
        $sanitized['version'] = (new VersionParser())->normalize($sanitized['version']);

        return new static(...$sanitized);
    }

    public static function comparableOf(?string $version = null): ?ConstraintInterface
    {
        $sanitized = static::sanitize($version);

        if (isset($sanitized['version'])) {
            return (new Version(... $sanitized))->comparable();
        }

        return null;
    }

    /**
     * Removes any train (like "-distributed" in "0.1.3-distributed") from the version constraint.
     * This has been the case for <em>loki:5.41.9-distributed</em>
     *
     * @param string|null $versionConstraint
     * @return array
     */
    public static function sanitize(?string $version = null): array
    {
        $r = ['raw' => $version, 'version' => null, 'flavor' => null];

        if ($version) {
            if (preg_match('/^(?<version>([^\-]*))(\-(?<flavor>(.*)))?$/', $r['raw'], $ret)) {
                $r['version'] = $ret['version'];
                // remove any characters and replace it with a single zero digit. There maybe some versions like 15.x.x
                $r['version'] = preg_replace('/[a-zA-Z]+/', '0', $r['version']);
                $r['flavor'] = $ret['flavor'] ?? null;
            }
        }

        return $r;
    }

    public function comparable(): ConstraintInterface
    {
        return (new VersionParser())->parseConstraints($this->version);
    }

    public function parts(): array
    {
        return explode(".", $this->version);
    }

    private function toVersion(array $parts): Version
    {
        return Version::of(implode(".", $parts));
    }

    public function nextMinor(): Version
    {
        $parts = $this->parts();

        return $this->toVersion([$parts[0], $parts[1] + 1, 0, 0]);
    }

    public function nextPatch(): Version
    {
        $parts = $this->parts();

        return $this->toVersion([$parts[0], $parts[1], $parts[2] + 1, 0]);
    }

    public function nextMajor(): Version
    {
        $parts = $this->parts();
        return $this->toVersion([$parts[0] + 1, 0, 0, 0]);
    }

    public function __toString()
    {
        return $this->version;
    }
}
