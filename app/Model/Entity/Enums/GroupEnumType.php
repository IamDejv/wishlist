<?php
declare(strict_types=1);

namespace App\Model\Entity\Enums;

class GroupEnumType extends EnumType
{
	protected string $name = "enumgrouptype";

	protected array $values = [
		self::BASIC,
		self::SECRET_SANTA,
	];

	const
		BASIC = "basic",
		SECRET_SANTA = "secret_santa"
	;

}
