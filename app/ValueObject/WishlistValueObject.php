<?php
declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;

class WishlistValueObject extends ValueObject
{
	/**
	 * @Assert\Type("string")
	 */
	public string $user;

	/**
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	public string $name;

	/**
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	public string $image;

	public function getUser(): string
	{
		return $this->user;
	}

	public function getName(): string
	{
		return $this->name;
	}

	public function getImage(): string
	{
		return $this->image;
	}
}
