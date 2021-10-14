<?php
declare(strict_types=1);

namespace App\ValueObject;


use Symfony\Component\Validator\Constraints as Assert;

class UserValueObject extends ValueObject
{
	/**
	 * @var string
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	public string $email;

	/**
	 * @var string
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	public string $firstname;

	/**
	 * @var string
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	public string $lastname;

	/**
	 * @var string
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	public string $phone1;

	/**
	 * @var string|null
	 * @Assert\Type("string")
	 */
	public ?string $phone2;

	/**
	 * @var int|null
	 * @Assert\Type("int")
	 */
	public ?int $role = 1;

	/**
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->email;
	}

	/**
	 * @return string
	 */
	public function getPassword(): string
	{
		return $this->password;
	}

	/**
	 * @return string
	 */
	public function getFirstname(): string
	{
		return $this->firstname;
	}

	/**
	 * @return string
	 */
	public function getLastname(): string
	{
		return $this->lastname;
	}

	/**
	 * @return string
	 */
	public function getPhone1(): string
	{
		return $this->phone1;
	}

	/**
	 * @return string|null
	 */
	public function getPhone2(): ?string
	{
		return $this->phone2;
	}

	/**
	 * @return int|null
	 */
	public function getRole(): ?int
	{
		return $this->role;
	}
}
