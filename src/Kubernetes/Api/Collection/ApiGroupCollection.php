<?php
namespace Dreitier\Alm\Inspecting\Kubernetes\Api\Collection;
use Dreitier\Alm\Inspecting\Kubernetes\Api\Model\ApiGroup;
use Maclof\Kubernetes\Collections\Collection;

class ApiGroupCollection extends Collection
{
    public function __construct(array $items)
    {
        parent::__construct($this->getItems($items));
    }

    protected function getItems(array $items): array
    {
        foreach ($items as &$item) {
            if ($item instanceof ApiGroup) {
                continue;
            }

            $item = new ApiGroup($item);
        }

        return $items;
    }

    private ?bool $hasRancherEndpoints = null;

    public function hasRancherEndpoints(): bool {
        if (null === $this->hasRancherEndpoints) {
            $this->hasRancherEndpoints = false;

            foreach ($this->items  as $item) {
                $name = ($item->toArray())['name'];

                if (strpos($name, "cattle.io") !== FALSE) {
                    $this->hasRancherEndpoints = true;
                    break;
                }
            }
        }

        return $this->hasRancherEndpoints;
    }
}
