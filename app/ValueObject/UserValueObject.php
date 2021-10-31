<?php
declare(strict_types=1);

namespace App\ValueObject;


use Symfony\Component\Validator\Constraints as Assert;

class UserValueObject extends ValueObject
{
	/**
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	public string $email;

	/**
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	public string $firstname;

	/**
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	public string $lastname;

	public function getEmail(): string
	{
		return $this->email;
	}

	public function getFirstname(): string
	{
		return $this->firstname;
	}

	public function getLastname(): string
	{
		return $this->lastname;
	}
}
