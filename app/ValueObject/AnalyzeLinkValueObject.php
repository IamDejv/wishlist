<?php
declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;

class AnalyzeLinkValueObject extends ValueObject
{
	/**
	 * @Assert\Type("string")
	 * @Assert\NotBlank
	 */
	public string $link;

	/**
	 * @return string
	 */
	public function getLink(): string
	{
		return $this->link;
	}
}
