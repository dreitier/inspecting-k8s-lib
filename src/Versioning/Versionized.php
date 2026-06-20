<?php

namespace Dreitier\Alm\Inspecting\Versioning;


interface Versionized
{
    public function getVersion(): Version;
}
