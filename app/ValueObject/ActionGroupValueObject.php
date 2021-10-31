<?php
declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;

class ActionGroupValueObject extends ValueObject
{
	/**
	 * @Assert\Type("int")
	 * @Assert\NotBlank
	 */
	public int $id;

	/**
	 * @Assert\Type("string")
	 * @Assert\NotBlank
	 */
	public string $action;

	public function getId(): int
	{
		return $this->id;
	}

	public function getAction(): string
	{
		return $this->action;
	}
}
