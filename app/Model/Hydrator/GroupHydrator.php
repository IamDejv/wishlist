<?php
declare(strict_types=1);

namespace App\Model\Hydrator;

use App\Model\Entity\BaseEntity;
use App\Model\Entity\Group;
use App\Model\Entity\Product;
use App\ValueObject\GroupValueObject;
use App\ValueObject\ValueObjectInterface;

class GroupHydrator implements HydratorInterface
{
	/**
	 * @param ValueObjectInterface|GroupValueObject $valueObject
	 * @param BaseEntity|null $entity
	 * @return Group|null
	 */
	public function hydrate(ValueObjectInterface|GroupValueObject $valueObject, ?BaseEntity $entity): ?Group
	{
		if (is_null($entity)) {
			$entity = new Group();
			$entity->setActive(false);
			$entity->setArchived(false);
		}

		$entity->setName($valueObject->getName());
		$entity->setImage($valueObject->getImage());
		$entity->setDescription($valueObject->getDescription());
		$entity->setType($valueObject->getType());

		return $entity;
	}
}
