<?php
declare(strict_types=1);

namespace App\Model\Hydrator;

use App\Model\Entity\BaseEntity;
use App\Model\Entity\Category;
use App\Model\Entity\Group;
use App\Model\Entity\Product;
use App\Model\Entity\User;
use App\Model\Entity\Wishlist;
use App\Model\EntityManager;
use App\ValueObject\GroupValueObject;
use App\ValueObject\ProductValueObject;
use App\ValueObject\ValueObjectInterface;
use App\ValueObject\WishlistValueObject;
use Doctrine\ORM\EntityNotFoundException;

class WishlistHydrator implements HydratorInterface
{
	public function __construct(private EntityManager $em)
	{}

	/**
	 * @param ValueObjectInterface|WishlistValueObject $valueObject
	 * @param BaseEntity|null $entity
	 * @return Product|null
	 * @throws EntityNotFoundException
	 */
	public function hydrate(ValueObjectInterface|WishlistValueObject $valueObject, ?BaseEntity $entity): ?Wishlist
	{
		if (is_null($entity)) {
			$entity = new Wishlist();
		}

		$user = $this->em->getRepository(User::class)->find($valueObject->getUser());

		if (!$user instanceof User) {
			throw new EntityNotFoundException("User not found");
		}

		$entity->setOwner($user);

		return $entity;
	}
}
