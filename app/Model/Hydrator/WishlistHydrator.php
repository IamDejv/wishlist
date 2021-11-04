<?php
declare(strict_types=1);

namespace App\Model\Hydrator;

use App\Model\Entity\BaseEntity;
use App\Model\Entity\Product;
use App\Model\Entity\User;
use App\Model\Entity\Wishlist;
use App\Model\EntityManager;
use App\ValueObject\ValueObjectInterface;
use App\ValueObject\WishlistValueObject;
use Doctrine\ORM\EntityNotFoundException;

class WishlistHydrator implements HydratorInterface
{
	/**
	 * @param ValueObjectInterface|WishlistValueObject $valueObject
	 * @param BaseEntity|null $entity
	 * @return Product|null
	 */
	public function hydrate(ValueObjectInterface|WishlistValueObject $valueObject, ?BaseEntity $entity): ?Wishlist
	{
		if (is_null($entity)) {
			$entity = new Wishlist();
			$entity->setArchived(false);
			$entity->setActive(false);
		}

		$entity->setImage($valueObject->getImage());
		$entity->setName($valueObject->getName());

		return $entity;
	}
}
