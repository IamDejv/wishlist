<?php
declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;

class UpdateProductValueObject extends ValueObject
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
	public string $image;

	/**
	 * @Assert\Type("string")
	 */
	public string $url;

	/**
	 * @Assert\Type("float")
	 */
	public float $price;

	public function getName(): string
	{
		return $this->name;
	}

	public function getDescription(): string
	{
		return $this->description;
	}

	public function getImage(): string
	{
		return $this->image;
	}

	public function getUrl(): string
	{
		return $this->url;
	}

	public function getPrice(): float
	{
		return $this->price;
	}
}
