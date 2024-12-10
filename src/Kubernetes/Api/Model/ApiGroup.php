<?php
namespace Dreitier\Alm\Inspecting\Kubernetes\Api\Model;
use Maclof\Kubernetes\Models\Model;

class ApiGroup extends Model
{
    protected string $apiVersion = '';

	public function getSpec(string $key): ?array
	{
		return isset($this->attributes['spec'][$key]) ? $this->attributes['spec'][$key] : null;
	}
}
