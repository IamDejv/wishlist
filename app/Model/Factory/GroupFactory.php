<?php

namespace App\Model\Factory;

use App\Model\Entity\BaseEntity;
use App\Model\Entity\Group;
use App\Model\Hydrator\GroupHydrator;
use App\ValueObject\GroupValueObject;
use App\ValueObject\ValueObjectInterface;

class GroupFactory implements EntityFactoryInterface
{
	public function __construct(private GroupHydrator $hydrator)
	{
	}

	/**
	 * @param ValueObjectInterface|GroupValueObject $valueObject
	 * @return BaseEntity|Group|null
	 */
	public function create(GroupValueObject|ValueObjectInterface $valueObject): BaseEntity|Group|null
	{
		return $this->hydrator->hydrate($valueObject, null);
	}
}
