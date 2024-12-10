<?php
namespace Dreitier\Alm\Inspecting\Kubernetes\Api\Collection\Rancher;
use Dreitier\Alm\Inspecting\Kubernetes\Api\Model\App;
use Dreitier\Alm\Inspecting\Kubernetes\Api\Model\Rancher\Cluster;
use Maclof\Kubernetes\Collections\Collection;

class ClusterCollection extends Collection
{
    public function __construct(array $items)
    {
        parent::__construct($this->getClusters($items));
    }

    protected function getClusters(array $items): array
    {
        foreach ($items as &$item) {
            if ($item instanceof Cluster) {
                continue;
            }

            $item = new Cluster($item);
        }

        return $items;
    }
}
