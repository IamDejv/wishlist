<?php

namespace App\Model\Factory;

use App\Model\Entity\BaseEntity;
use App\Model\Entity\Category;
use App\Model\Hydrator\CategoryHydrator;
use App\ValueObject\CategoryValueObject;
use App\ValueObject\ValueObjectInterface;

class CategoryFactory implements EntityFactoryInterface
{
	public function __construct(private CategoryHydrator $hydrator)
	{
	}

	/**
	 * @param ValueObjectInterface|CategoryValueObject $valueObject
	 * @return BaseEntity|Category|null
	 */
	public function create(CategoryValueObject|ValueObjectInterface $valueObject): BaseEntity|Category|null
	{
		return $this->hydrator->hydrate($valueObject, null);
	}
}
