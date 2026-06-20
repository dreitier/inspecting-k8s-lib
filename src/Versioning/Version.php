<?php

namespace Dreitier\Alm\Inspecting\Versioning;

use Composer\Semver\Constraint\ConstraintInterface;
use Composer\Semver\VersionParser;

class Version
{
	private function __construct(
		public readonly string  $raw,
		public readonly int     $major = 0,
		public readonly int     $minor = 0,
		public readonly int     $patch = 0,
		public readonly int     $buildnumber = 0,
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

	public static function toVersion(Version|Versionized $version): Version
	{
		if ($version instanceof Version) {
			return $version;
		}

		return $version->getVersion();
	}

	public static function of(string $version): Version
	{
		$regex = '/^(?<prelude>[a-zA-Z]*)(?<major>0|[1-9]\d*)(\.(?<minor>0|[1-9]\d*)(\.(?<patch>0|[1-9]\d*)(\.(?<buildnumber>0|[1-9]\d*))?)?)?(-(?<prerelease>[0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?(\+(?<build>[0-9A-Za-z-]+(?:\.[0-9A-Za-z-]+)*))?$/';

		if (preg_match($regex, $version, $matches)) {
			$args = [
				'raw' => $version,
				'major' => (int)$matches['major'],
			];

			if (isset($matches['minor'])) {
				$args['minor'] = (int)$matches['minor'];
			}

			if (isset($matches['patch'])) {
				$args['patch'] = (int)$matches['patch'];
			}

			if (isset($matches['buildnumber'])) {
				$args['buildnumber'] = (int)$matches['buildnumber'];
			}

			if (isset($matches['prerelease'])) {
				$args['flavor'] = $matches['prerelease'];
			}

			if (isset($matches['build'])) {
				$args['flavor'] = $matches['build'];
			}

			return new Version(...$args);
		}

		throw new \Exception("Could not parse version string '$version'");
	}

	public function compare(Version|Versionized $version, bool $includeFlavorCheck = false): int
	{
		$version = static::toVersion($version);

		if (0 !== ($result = $this->_compare($this->major, $version->major))) {
			return $result;
		}

		if (0 !== ($result = $this->_compare($this->minor, $version->minor))) {
			return $result;
		}

		if (0 !== ($result = $this->_compare($this->patch, $version->patch))) {
			return $result;
		}

		if (0 !== ($result = $this->_compare($this->buildnumber, $version->buildnumber))) {
			return $result;
		}

		if ($includeFlavorCheck) {
			if ($this->flavor !== null) {
				return $this->flavorAsVersion()->compare($version);
			}
		}

		return 0;
	}

	private function _compare(int $left, int $right): int
	{
		if ($left > $right) {
			return 1;
		}

		if ($left < $right) {
			return -1;
		}

		return 0;
	}

	/**
	 * True if *this* version is higher (newer, after) than the other
	 * @param Version|Versionized $version
	 * @return bool
	 */
	public function isHigherThan(Version|Versionized $version): bool
	{
		return $this->compare(static::toVersion($version)) >= 1;
	}

	public function isEqual(Version|Versionized  $version): bool
	{
		return $this->compare(static::toVersion($version), true) == 0;
	}

	/**
	 * True if *this* version is lower (older, before) than the other
	 * @param Version|Versionized $version
	 * @return bool
	 */
	public function isLowerThan(Version|Versionized $version): bool
	{
		return $this->compare(static::toVersion($version)) <= -1;
	}

	public function main(bool $withBuildnumber = false): string
	{
		$r = $this->major . '.' . $this->minor . '.' . $this->patch;
		if ($withBuildnumber) {
			$r .= '.' . $this->buildnumber;
		}

		return $r;
	}

	public function flavorAsVersion(): ?Version {
		if ($this->flavor) {
			return Version::of($this->flavor);
		}
		return null;
	}

	public function __toString()
	{
		return $this->raw;
	}
}
