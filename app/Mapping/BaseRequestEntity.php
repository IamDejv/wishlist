<?php
declare(strict_types=1);

namespace App\Mapping;


use Apitte\Core\Mapping\Request\BasicEntity;

class BaseRequestEntity extends BasicEntity
{
	use TReflectionProperties;

	/**
	 * @return mixed[]
	 */
	public function getRequestProperties(): array
	{
		return $this->getProperties();
	}
}
