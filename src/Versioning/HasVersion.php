<?php

namespace Dreitier\Alm\Inspecting\Versioning;

trait HasVersion
{
    public function getVersion(): Version
    {
        return $this->version;
    }
}
