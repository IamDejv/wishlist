<?php
declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;

class GroupValueObject extends ValueObject
{
	/**
	 * @var string
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	public string $name;

	/**
	 * @var string
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	public string $description;

	/**
	 * @var string
	 * @Assert\Type("string")
	 */
	public string $type;

	/**
	 * @var bool
	 * @Assert\Type("bool")
	 */
	public bool $public;

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription(string $description): void
	{
		$this->description = $description;
	}

	/**
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType(string $type): void
	{
		$this->type = $type;
	}

	/**
	 * @return bool
	 */
	public function isPublic(): bool
	{
		return $this->public;
	}

	/**
	 * @param bool $public
	 */
	public function setPublic(bool $public): void
	{
		$this->public = $public;
	}
}
