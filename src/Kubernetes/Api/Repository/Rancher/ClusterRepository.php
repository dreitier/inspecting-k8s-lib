<?php
namespace Dreitier\Alm\Inspecting\Kubernetes\Api\Repository\Rancher;

use Dreitier\Alm\Kubernetes\Api\Collection\AppCollection;
use Dreitier\Alm\Kubernetes\Api\Collection\Rancher\ClusterCollection;
use Dreitier\Alm\Kubernetes\Api\Model\Rancher\Cluster;
use Maclof\Kubernetes\Repositories\Repository;

class ClusterRepository extends Repository {
	protected ?string $apiVersion = 'management.cattle.io/v3';
	protected string $uri = 'clusters';
	protected bool $namespace = false;

	protected function createCollection($response): ClusterCollection
    {
        return new ClusterCollection($response['items']);
    }
}
