<?php

namespace Dreitier\Alm\Inspecting\Source;

interface ProvidesChangelog
{
    public function changelog(...$args): Changelog;
}
