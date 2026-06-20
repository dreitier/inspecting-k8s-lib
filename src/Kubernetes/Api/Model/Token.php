<?php

namespace Dreitier\Alm\Inspecting\Kubernetes\Api\Model;

use Maclof\Kubernetes\Models\Model;

class Token extends Model
{
    protected string $apiVersion = 'catalog.cattle.io/v1';

    public function getSpec(string $key): ?array
    {
        return isset($this->attributes['spec'][$key]) ? $this->attributes['spec'][$key] : null;
    }

    public function getDescription(): ?string
    {
        return $this->attributes['description'] ?? null;
    }

    public function getExpiresAt(): ?string
    {
        return $this->attributes['expiresAt'] ?? null;
    }

    public function isExpired(): bool
    {
        return (bool)$this->attributes['expired'];
    }

    public function getKind(): ?string
    {
        return $this->attributes['kind'] ?? null;
    }

    public function getToken(): ?string
    {
        return $this->attributes['token'] ?? null;
    }

    public function getTtl(): ?int
    {
        return (int)$this->attributes['ttl'] ?? null;
    }

    public function getUserId(): ?string {
        return $this->attributes['userId'] ?? null;
    }
    public function getCreatedAt(): ?string
    {
        return $this->getMetadata('creationTimestamp');
    }

    public function getName(): ?string
    {
        return $this->getMetadata('name');
    }
}
