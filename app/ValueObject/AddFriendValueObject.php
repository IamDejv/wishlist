<?php
declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;

class AddFriendValueObject extends ValueObject
{
	/**
	 * @Assert\Type("string")
	 * @Assert\NotBlank
	 */
	public string $id;

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}
}
