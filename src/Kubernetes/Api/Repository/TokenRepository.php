<?php
namespace Dreitier\Alm\Inspecting\Kubernetes\Api\Repository;;

use Dreitier\Alm\Inspecting\Kubernetes\Api\Collection\TokenCollection;
use Maclof\Kubernetes\Repositories\Repository;

class TokenRepository extends Repository
{
	protected string $uri = 'tokens';
    protected bool $namespace = false;
    protected ?string $apiVersion = 'management.cattle.io/v3';

	protected function createCollection($response): TokenCollection
	{
		return new TokenCollection($response['items']);
	}
}
