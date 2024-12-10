<?php

namespace Dreitier\Alm\Inspecting\Helm\Chart;

use Dreitier\Alm\Source\Artifact;
use Dreitier\Alm\Source\ArtifactReference;
use Dreitier\Alm\Versioning\Version;

/**
 * Encapsulates a Helm chart release from artifacthub.io.
 * A Helm chart has a chart version, an application version and 0..n references to other source artifacts.
 * Those references can be either Helm chart dependencies or container images.
 */
class Release
{
    /**
     * @param Version $chartVersion version of the Helm chart
     * @param ArtifactReference $application Helm chart has been written for this application
     * @param array $references References to other source artifacts (container images, Helm dependencies)
     * @param array $raw deserialized response from artifacthub.io
     */
    public function __construct(
        public readonly Version           $chartVersion,
        public readonly ArtifactReference $application,
        public readonly array             $references = [],
        public readonly array             $raw = [],
    )
    {
    }

    private ?array $availableVersions = null;

    public function getAvailableVersions(): array
    {
        if ($this->availableVersions == null) {
            $r = [];

            foreach ($this->raw['available_versions'] as $available_version) {
                $r[] = Version::of($available_version['version']);
            }

            $this->availableVersions = $r;
        }

        return $this->availableVersions;
    }

    /**
     * Matches versionized strings like 'https://github.com/organization/project:abcdef-123'
     * @param string $url
     * @return string[]|null ['provider' => '', 'organization' => '', 'project' => '', 'version' => '']
     */
    static function matchVersionizedUrl(string $url)
    {
        if (preg_match('/^(?<provider>.*)\/(?<organization>([^\/]*))\/(?<project>([^\/|^:]*))(:(?<version>[a-z0-9\-\.]+))?$/m', $url, $r)) {
            return $r;
        }

        return null;
    }

    /**
     * Create a SourceArtifact for a Helm chart dependency
     * @param string $name
     * @param string $url
     * @param string|null $version
     * @return Artifact
     */
    public static function createHelmChartDependency(string $name, string $url, ?string $version = null): Artifact
    {
        $r = parse_url($url);

        $scheme = $r['scheme'];
        $hostWithPath = $r['host'] . '/' . str_replace('/', '', $r['path']) . '/' . $name;
        $providerPath = null;
        $projectPath = null;

        if ($r = static::matchVersionizedUrl($hostWithPath)) {
            $providerPath = $r['provider'];
            $projectPath = $r['organization'] . '/' . $r['project'];
            $name = $r['project'];
        }

        return new Artifact(
            type: 'helm',
            full: $url,
            scheme: $scheme,
            providerPath: $providerPath,
            projectPath: $projectPath,
            name: $name,
            version: Version::of($version),
        );
    }

    /**
     * Create SourceArtifact for a container image
     * @param string $url
     * @return Artifact
     */
    public static function createContainerImage(string $url): Artifact
    {
        if ($r = static::matchVersionizedUrl($url)) {
            $providerPath = $r['provider'];
            $projectPath = $r['organization'] . '/' . $r['project'];
            $name = $r['project'];
            $version = $r['version'];
        }

        return new Artifact(type: 'container_image',
            full: $url,
            providerPath: $providerPath,
            projectPath: $projectPath,
            name: $name,
            version: Version::of($version),
        );
    }

    /**
     * Create new Helm chart release based upon retrieved information from artifacthub.io.
     * TODO This should be probably refactored and moved to the Artifacthub.io source provider.
     * @param array $content
     * @return Release
     */
    public static function ofArtifactoryHub(array $content): Release
    {
        // application name is a best-guess only
        $applicationName = $content['name'] ?? $content['repository']['name'];

        $mainApplicationReference = null;
        $references = [];

        // in a Helm chart connected to artifacthub.io we have 4 ways to identify the chart's, application's
        // dependencies' name and version:
        // 1. `annotations[artifacthub.io/images]`
        //      Can contain a list of directly referenced images in this chart
        // 2. `annotations[artifacthub.io/links]`
        //      Can contain a list of links to the application and chart repository
        // 3. `annotations[images]`
        //      Can contain a list of container images, required for the direct working of this chart, e.g. in https://github.com/bitnami/charts/blob/main/bitnami/keycloak/Chart.yaml
        //      artifacthub.io seems to use this to map it to the `.containers_image` field
        // 4. `sources`
        //      A list of URLs with sources. This is mapped by artifacthub.io to the `.links` field
        // 5. Calling the `/api/v1/packages/${packageID}/${version}/security-report` endpoint. It contains all referenced images of this chart

        if (isset($content['containers_image']) && is_array($content['containers_image'])) {
            foreach ($content['containers_image'] as $containerImage) {
                $artifactSource = static::createContainerImage($containerImage['image']);

                $ar = new ArtifactReference(
                    name: $artifactSource->name ?? '',
                    version: Version::of($artifactSource->version ?? '*'),
                    sourceArtifact: $artifactSource
                );

                // if the image name equals the application name, this is *maybe* the application reference
                if ($ar->name == $applicationName) {
                    $mainApplicationReference = $ar;
                }

                $references[] = $ar;
            }
        }

        if (isset($content['data']) && isset($content['data']['dependencies']) && is_array($content['data']['dependencies'])) {
            foreach ($content['data']['dependencies'] as $dependency) {
                $ar = new ArtifactReference(name: $dependency['name'],
                    version: Version::of($dependency['version']),
                    sourceArtifact: static::createHelmChartDependency(
                        $dependency['name'],
                        $dependency['repository'],
                        $dependency['version'],
                    ),
                );

                // TODO duplicate ^^
                // if the name of the Helm chart dependency equals the applications name, than this is *maybe* the application reference
                if ($ar->name == $applicationName) {
                    $mainApplicationReference = $ar;
                }

                $references[] = $ar;
            }
        }

        // fallback to "anonymous" source artifact for the application
        if (!$mainApplicationReference) {
            $mainApplicationReference = new Artifact('exe');
        }

        $application = new ArtifactReference(
            name: $applicationName,
            version: Version::of($content['app_version']),
            sourceArtifact: $mainApplicationReference,
        );

        return new Release(
            chartVersion: Version::of($content['version']),
            application: $application,
            references: $references,
            raw: $content,
        );
    }
}
