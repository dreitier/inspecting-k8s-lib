<?php
namespace Dreitier\Alm\Inspecting\Kubernetes\Api;

class DeployedApplications {
	private array $items = [];

	public function add(DeployedApplication $item): DeployedApplications {
		$this->items[] = $item;
		return $this;
	}

	public function items(): \Generator {
		foreach ($this->items as $item) {
			yield $item;
		}
	}

	public function toArray(): array {
		$r = [];

		foreach ($this->items as $item) {
			$r[] = $item->toArray();
		}

		return $r;
	}

	public static function fromArray(array $args): DeployedApplications {
		$r = new DeployedApplications();

		foreach ($args as $item) {
			$r->add(DeployedApplication::fromArray($item));
		}

		return $r;
	}
}
