<?php
declare(strict_types=1);

namespace App\Model\Hydrator;

use App\Model\Entity\BaseEntity;
use App\Model\Entity\Product;
use App\Model\Entity\Wishlist;
use App\Model\EntityManager;
use App\ValueObject\ProductValueObject;
use App\ValueObject\UpdateProductValueObject;
use App\ValueObject\ValueObjectInterface;

class ProductHydrator implements HydratorInterface
{
	public function __construct(private EntityManager $em)
	{
	}

	/**
	 * @param ProductValueObject|UpdateProductValueObject|ValueObjectInterface $valueObject
	 * @param BaseEntity|null $entity
	 * @return Product|null
	 */
	public function hydrate(ProductValueObject|UpdateProductValueObject|ValueObjectInterface $valueObject, ?BaseEntity $entity): ?Product
	{
		if (is_null($entity)) {
			$entity = new Product();
			$entity->setReserved(false);

			// Wishlist must be set in creation process
			$wishlist = $this->em->getRepository(Wishlist::class)->find($valueObject->getWishlistId());
			$entity->setWishlist($wishlist);
		}

		$entity->setName($valueObject->getName());
		$entity->setDescription($valueObject->getDescription());
		$entity->setImage($valueObject->getImage());
		$entity->setPrice($valueObject->getPrice());
		$entity->setUrl($valueObject->getUrl());

		return $entity;
	}
}
