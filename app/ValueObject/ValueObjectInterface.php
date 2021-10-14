<?php
declare(strict_types=1);

namespace App\ValueObject;


interface ValueObjectInterface
{
	/**
	 * Validate function, which assert annotations in ValueObjects
	 * @return $this|null
	 */
	public function validate(): ?self;
}
