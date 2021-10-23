<?php
declare(strict_types=1);

namespace App\Model\Hydrator;

use App\Model\Entity\BaseEntity;
use App\Model\Entity\Category;
use App\Model\Entity\Group;
use App\Model\Entity\Product;
use App\Model\EntityManager;
use App\ValueObject\CategoryValueObject;
use App\ValueObject\GroupValueObject;
use App\ValueObject\ProductValueObject;
use App\ValueObject\ValueObjectInterface;

class CategoryHydrator implements HydratorInterface
{
	public function __construct(private EntityManager $em)
	{
	}

	/**
	 * @param ValueObjectInterface|CategoryValueObject $valueObject
	 * @param BaseEntity|null $entity
	 * @return Category|null
	 */
	public function hydrate(ValueObjectInterface|CategoryValueObject $valueObject, ?BaseEntity $entity): ?Category
	{
		if (is_null($entity)) {
			$entity = new Category();
		}

		$entity->setName($valueObject->getName());
		$entity->setImage($valueObject->getImage());

		if (!is_null($valueObject->getParent())) {
			$parent = $this->em->getRepository(Category::class)->find($valueObject->getParent());

			$entity->setParent($parent);
		}

		return $entity;
	}
}
