<?php
namespace Dreitier\Alm\Inspecting\Kubernetes\Api\Collection;

use Dreitier\Alm\Inspecting\Kubernetes\Api\Model\Token;
use Maclof\Kubernetes\Collections\Collection;

class TokenCollection extends Collection
{
	/**
	 * The constructor.
	 */
	public function __construct(array $items)
	{
		parent::__construct($this->getSecrets($items));
	}

	/**
	 * Get an array of secrets.
	 */
	protected function getSecrets(array $items): array
	{
		foreach ($items as &$item) {
			if ($item instanceof Token) {
				continue;
			}
			
			$item = new Token($item);
		}

		return $items;
	}
}
