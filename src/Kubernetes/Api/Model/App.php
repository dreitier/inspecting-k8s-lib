<?php
namespace Dreitier\Alm\Inspecting\Kubernetes\Api\Model;
use Maclof\Kubernetes\Models\Model;

class App extends Model
{
    protected string $apiVersion = 'catalog.cattle.io/v1';

	public function getSpec(string $key): ?array
	{
		return isset($this->attributes['spec'][$key]) ? $this->attributes['spec'][$key] : null;
	}
}
