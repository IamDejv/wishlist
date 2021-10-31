<?php
declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;

class ActionFriendValueObject extends ActionUserValueObject
{
	/**
	 * @Assert\Type("string")
	 * @Assert\NotBlank
	 */
	public string $id;

	/**
	 * @Assert\Type("string")
	 * @Assert\NotBlank
	 */
	public string $action;

	public function getId(): string
	{
		return $this->id;
	}

	public function getAction(): string
	{
		return $this->action;
	}
}
