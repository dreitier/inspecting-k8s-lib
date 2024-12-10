<?php
namespace Dreitier\Alm\Inspecting\Kubernetes\Api\Repository;

use Dreitier\Alm\Inspecting\Kubernetes\Api\Collection\ApiGroupCollection;
use Maclof\Kubernetes\Repositories\Repository;

class ApiGroupRepository extends Repository {
	protected string $uri = '';
	protected bool $namespace = false;

	protected function createCollection($response): ApiGroupCollection
    {
        return new ApiGroupCollection($response['groups']);
    }

    protected function getApiVersion(): ?string {
        // we have to override this method so that no '/api[s]/v{1,2,...}' is automatically generated but '/apis' as root entry is used
        return '../apis';
    }
}
