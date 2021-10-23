<?php
declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;

class CategoryValueObject extends ValueObject
{
	/**
	 * @Assert\Type("int")
	 */
	public ?int $parent;

	/**
	 * @Assert\NotNull()
	 * @Assert\NotBlank()
	 * @Assert\Type("string")
	 */
	public string $name;

	/**
	 * @Assert\Type("string")
	 */
	public ?string $image;

	/**
	 * @return int|null
	 */
	public function getParent(): ?int
	{
		return $this->parent;
	}

	/**
	 * @param int $parent
	 */
	public function setParent(int $parent): void
	{
		$this->parent = $parent;
	}

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
	 * @return string|null
	 */
	public function getImage(): ?string
	{
		return $this->image;
	}

	/**
	 * @param string|null $image
	 */
	public function setImage(?string $image): void
	{
		$this->image = $image;
	}
}
