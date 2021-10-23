<?php

namespace App\Model\Factory;

use App\Model\Entity\BaseEntity;
use App\Model\Entity\Product;
use App\Model\Hydrator\ProductHydrator;
use App\ValueObject\ProductValueObject;
use App\ValueObject\ValueObjectInterface;

class ProductFactory implements EntityFactoryInterface
{
	public function __construct(private ProductHydrator $hydrator)
	{
	}

	/**
	 * @param ValueObjectInterface|ProductValueObject $valueObject
	 * @return BaseEntity|Product|null
	 */
	public function create(ProductValueObject|ValueObjectInterface $valueObject): BaseEntity|Product|null
	{
		return $this->hydrator->hydrate($valueObject, null);
	}
}
