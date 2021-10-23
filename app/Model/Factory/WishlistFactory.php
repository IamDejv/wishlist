<?php

namespace App\Model\Factory;

use App\Model\Entity\BaseEntity;
use App\Model\Entity\Group;
use App\Model\Entity\Wishlist;
use App\Model\Hydrator\GroupHydrator;
use App\Model\Hydrator\WishlistHydrator;
use App\ValueObject\GroupValueObject;
use App\ValueObject\ValueObjectInterface;
use App\ValueObject\WishlistValueObject;
use Doctrine\ORM\EntityNotFoundException;

class WishlistFactory implements EntityFactoryInterface
{
	public function __construct(private WishlistHydrator $hydrator)
	{
	}

	/**
	 * @param ValueObjectInterface|WishlistValueObject $valueObject
	 * @return BaseEntity|Wishlist|null
	 * @throws EntityNotFoundException
	 */
	public function create(WishlistValueObject|ValueObjectInterface $valueObject): Wishlist|BaseEntity|null
	{
		return $this->hydrator->hydrate($valueObject, null);
	}
}
