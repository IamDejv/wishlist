<?php
declare(strict_types=1);

namespace App\Model\ResponseMapper;


use JetBrains\PhpStorm\ArrayShape;
use App\Model\Entity\BaseEntity;
use App\Model\Entity\User;
use JetBrains\PhpStorm\Pure;

class UserResponseMapper implements ResponseMapperInterface
{
	#[Pure]
	#[ArrayShape(["firstname" => "null|string", "lastname" => "null|string", "email" => "string", "id" => "string"])]
	public function toArray(BaseEntity|User $entity): array
	{
		return [
			"firstname" => $entity->getFirstname(),
			"lastname" => $entity->getLastname(),
			"email" => $entity->getEmail(),
			"id" => $entity->getId(),
		];
	}

}
