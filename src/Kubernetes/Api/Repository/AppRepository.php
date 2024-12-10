<?php
namespace Dreitier\Alm\Inspecting\Kubernetes\Api\Repository;

use Dreitier\Alm\Inspecting\Kubernetes\Api\Collection\AppCollection;
use Maclof\Kubernetes\Repositories\Repository;

class AppRepository extends Repository {
	protected ?string $apiVersion = 'catalog.cattle.io/v1';
	protected string $uri = 'apps';
	protected bool $namespace = false;

	protected function createCollection($response): AppCollection
    {
        return new AppCollection($response['items']);
    }
}
