<?php
declare(strict_types=1);

namespace App\Model\Hydrator;

use App\Model\Entity\BaseEntity;
use App\Model\Entity\Category;
use App\Model\Entity\Group;
use App\Model\Entity\Product;
use App\Model\EntityManager;
use App\ValueObject\GroupValueObject;
use App\ValueObject\ProductValueObject;
use App\ValueObject\ValueObjectInterface;

class GroupHydrator implements HydratorInterface
{
	/**
	 * @param ValueObjectInterface|GroupValueObject $valueObject
	 * @param BaseEntity|null $entity
	 * @return Product|null
	 */
	public function hydrate(ValueObjectInterface|GroupValueObject $valueObject, ?BaseEntity $entity): ?Group
	{
		if (is_null($entity)) {
			$entity = new Group();
		}

		$entity->setName($valueObject->getName());
		$entity->setDescription($valueObject->getDescription());
		$entity->setPublic($valueObject->isPublic());
		$entity->setType($valueObject->getType());

		return $entity;
	}
}
