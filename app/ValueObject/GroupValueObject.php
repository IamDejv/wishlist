<?php
declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;

class GroupValueObject extends ValueObject
{
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
	public string $description;

	/**
	 * @Assert\Type("string")
	 */
	public string $type;

	/**
	 * @Assert\Type("string")
	 */
	public string $image;

	/**
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	public string $user;

	public function getName(): string
	{
		return $this->name;
	}

	public function setName(string $name): void
	{
		$this->name = $name;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function setDescription(string $description): void
	{
		$this->description = $description;
	}

	public function getType(): string
	{
		return $this->type;
	}

	public function setType(string $type): void
	{
		$this->type = $type;
	}

	public function getImage(): string
	{
		return $this->image;
	}

	public function getUser(): string
	{
		return $this->user;
	}
}
