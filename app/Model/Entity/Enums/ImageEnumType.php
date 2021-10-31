<?php
declare(strict_types=1);

namespace App\Model\Entity\Enums;

class ImageEnumType extends EnumType
{
	protected string $name = "enumimagetype";

	protected array $values = [
		self::GROUP,
	];

	const
		GROUP = "group"
	;

}
