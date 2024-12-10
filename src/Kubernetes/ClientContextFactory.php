<?php

namespace Dreitier\Alm\Inspecting\Kubernetes;

use Dreitier\Alm\Inspecting\Kubernetes\Api\Repository\ApiGroupRepository;
use Dreitier\Alm\Inspecting\Kubernetes\Api\Repository\AppRepository;
use Dreitier\Alm\Inspecting\Kubernetes\Api\Repository\Rancher\ClusterRepository;
use Maclof\Kubernetes\Client;
use Maclof\Kubernetes\RepositoryRegistry;
use Symfony\Component\Yaml\Yaml;

class ClientContextFactory
{
    public function __construct(public readonly array $registerAdditionalRepositories = [])
    {
    }

    public function create(
        string           $endpoint,
        null|bool|string $withCAVerificationOf = null,
        ?string          $pathToClientCertificate = null,
        ?string          $pathToClientKey = null,
        ?string          $basicAuthUsername = null,
        ?string          $basicAuthPassword = null,
        ?string          $token = null,
    )
    {
        $httpClientArgs = [];
        $options = [];

        if ($withCAVerificationOf !== null) {
            $httpClientArgs['verify'] = $withCAVerificationOf;
        }

        if ($pathToClientCertificate && $pathToClientKey) {
            $httpClientArgs['cert'] = $pathToClientCertificate;
            $httpClientArgs['ssl_key'] = $pathToClientKey;
        }

        if ($endpoint) {
            $options['master'] = $endpoint;
        }

        if ($token) {
            $options['token'] = $token;
        }

        if ($basicAuthUsername && $basicAuthPassword) {
            $options['username'] = $basicAuthUsername;
            $options['password'] = $basicAuthPassword;
        }

        $httpClient = !empty($httpClientArgs) ? new \GuzzleHttp\Client($httpClientArgs) : null;

        return new Client($options, $this->createRegistry(), $httpClient);
    }

    private function createRegistry(): RepositoryRegistry
    {
        $registry = new RepositoryRegistry();

        $customRepositories = [
            'api_groups' => ApiGroupRepository::class,
            'apps' => AppRepository::class,
            'rancher_clusters' => ClusterRepository::class,
        ];

        $useRepositoriesToRegister = array_merge($customRepositories, $this->registerAdditionalRepositories);

        foreach ($useRepositoriesToRegister as $name => $instance) {
            $registry[$name] = $instance;
        }

        return $registry;
    }

    public function createFromKubeConfig(
        ?string $kubeConfigPath = null,
        ?string $user = null,
        ?string $cluster = null,
        ?string $context = null

    ): ClientContext
    {
        if (empty($kubeConfigPath)) {
            throw new \Exception("parameter 'kube-config' not present");
        }

        if (!File::exists($kubeConfigPath)) {
            throw new \Exception("kube.config file " . $kubeConfigPath . " does not exist");
        }

        $config = Yaml::parse(file_get_contents($kubeConfigPath));
        $clusterContext = $this->loadClusterContext($config,
            $user,
            $cluster,
            $context
        );

        $client = new Client([
            'master' => $clusterContext->endpoint,
            'token' => $clusterContext->token,
        ], $this->createRegistry());

        return new ClientContext($clusterContext->clusterName, $clusterContext->endpoint, $client);
    }

    private function loadClusterContext(array       $configuration,
                                        string|null $user,
                                        string|null $clusterName,
                                        string|null $context,
    ): ClusterContext
    {
        $defaultContext = $context ?? ($configuration['current-context'] ?? null);

        $cluster = $this->getCluster($configuration, $clusterName, $defaultContext);
        throw_if(empty($cluster), "Unable to find cluster '" . $clusterName . "'");

        $token = $this->getToken($configuration, $user, $defaultContext);
        throw_if(!$token, "Unable to find token for user '" . $user . "'");

        return new ClusterContext($cluster['name'], $cluster['endpoint'], $token);
    }

    private function getCluster(array $configuration, string|null $useCluster, string|null $defaultContext): ?array
    {
        throw_if(!isset($configuration['clusters']), ".clusters does not exist");
        $fallback = null;

        $flatten = fn($cluster) => ['name' => $cluster['name'], 'endpoint' => $cluster['cluster']['server']];

        foreach ($configuration['clusters'] as $idx => $cluster) {
            if ($cluster['name'] == $useCluster) {
                return $flatten($cluster);
            }

            if ($cluster['name'] == $defaultContext) {
                $fallback = $flatten($cluster);
            }
        }

        return $fallback;
    }

    private function getToken(array $configuration, string|null $useUser, string|null $defaultContext): string
    {
        throw_if(!isset($configuration['users']), ".users does not exist");
        $fallback = null;

        foreach ($configuration['users'] as $idx => $user) {
            if ($user['name'] == $useUser) {
                return $user['user']['token'];
            }

            if ($user['name'] == $defaultContext) {
                $fallback = $user['user']['token'];
            }

        }

        return $fallback;
    }
}
