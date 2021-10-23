<?php
declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;

class WishlistValueObject extends ValueObject
{
	/**
	 * @var string
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	public string $user;

	/**
	 * @return string
	 */
	public function getUser(): string
	{
		return $this->user;
	}
}
