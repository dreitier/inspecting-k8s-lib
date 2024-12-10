<?php

namespace Dreitier\Alm\Inspecting\Kubernetes\Distribution;

enum Type
{
    case RANCHER_RKE1;
    case RANCHER_RKE2;
    case OPENSHIFT;
    case K3S;
}
