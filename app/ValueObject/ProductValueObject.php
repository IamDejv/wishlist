<?php
declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;

class ProductValueObject extends ValueObject
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
	public string $desc;

	/**
	 * @var string
	 * @Assert\Type("string")
	 */
	public string $image;

	/**
	 * @var string
	 * @Assert\Type("string")
	 */
	public string $url;

	/**
	 * @Assert\Type("float")
	 */
	public float $price;

	/**
	 * @Assert\Type("array")
	 */
	public array $categories;

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getDesc(): string
	{
		return $this->desc;
	}

	/**
	 * @return string
	 */
	public function getImage(): string
	{
		return $this->image;
	}

	/**
	 * @return string
	 */
	public function getUrl(): string
	{
		return $this->url;
	}

	/**
	 * @return float
	 */
	public function getPrice(): float
	{
		return $this->price;
	}

	/**
	 * @return array
	 */
	public function getCategories(): array
	{
		return $this->categories;
	}
}
