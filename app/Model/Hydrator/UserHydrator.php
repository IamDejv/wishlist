<?php
declare(strict_types=1);

namespace App\Model\Hydrator;


use App\Model\Entity\BaseEntity;
use App\Model\Entity\User;
use App\ValueObject\UserValueObject;
use App\ValueObject\ValueObjectInterface;

class UserHydrator implements HydratorInterface
{
	/**
	 * @param ValueObjectInterface|UserValueObject $valueObject
	 * @param BaseEntity|null $entity
	 * @return User
	 */
	public function hydrate(ValueObjectInterface|UserValueObject $valueObject, ?BaseEntity $entity = null): User
	{
		if (is_null($entity)) {
			$entity = new User();
		}

		$entity->setEmail($valueObject->getEmail());
		$entity->setFirstname($valueObject->getFirstname());
		$entity->setLastname($valueObject->getLastname());

		return $entity;
	}

}
