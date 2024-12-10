<?php

namespace Dreitier\Alm\Inspecting\Helm;

class Registry
{
    private array $byAlias = [];

    private array $byRegex = [];

    public function get($name): ?Chart
    {
        if (isset($this->byAlias[$name])) {
            return $this->byAlias[$name];
        }

        foreach ($this->byRegex as $regex => $chart) {
            if (preg_match($regex, $name)) {
                return $chart;
            }
        }

        return null;
    }

    private function isRegex(string $matcher): bool
    {
        return !preg_match("/^[a-zA-Z0-9\-]*$/", $matcher);
    }

    public function add(string|array $alias, Chart $helmChart): Registry
    {
        if (is_array($alias)) {
            foreach ($alias as $singleAlias) {
                $this->add($singleAlias, $helmChart);
            }
        } else {
            if ($this->isRegex($alias)) {
                $this->byRegex[$alias] = $helmChart;
            } else {
                $this->byAlias[$alias] = $helmChart;
            }
        }

        return $this;
    }
}
