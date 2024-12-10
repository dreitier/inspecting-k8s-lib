<?php
namespace Dreitier\Alm\Inspecting\Kubernetes\Api\Model\Rancher;
use Maclof\Kubernetes\Models\Model;

class Cluster extends Model
{
    protected string $apiVersion = 'management.cattle.io/v3';

	public function getSpec(string $key): ?array
	{
		return isset($this->attributes['spec'][$key]) ? $this->attributes['spec'][$key] : null;
	}
}
