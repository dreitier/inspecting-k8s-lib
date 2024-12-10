<?php
namespace Dreitier\Alm\Inspecting\Kubernetes\Api\Collection;
use Dreitier\Alm\Inspecting\Kubernetes\Api\Model\App;
use Maclof\Kubernetes\Collections\Collection;

class AppCollection extends Collection
{
    public function __construct(array $items)
    {
        parent::__construct($this->getApps($items));
    }

    protected function getApps(array $items): array
    {
        foreach ($items as &$item) {
            if ($item instanceof App) {
                continue;
            }

            $item = new App($item);
        }

        return $items;
    }
}
