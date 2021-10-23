<?php
declare(strict_types=1);

namespace App\Model\Hydrator;

use App\Model\Entity\BaseEntity;
use App\Model\Entity\Category;
use App\Model\Entity\Product;
use App\Model\EntityManager;
use App\ValueObject\ProductValueObject;
use App\ValueObject\ValueObjectInterface;

class ProductHydrator implements HydratorInterface
{
	public function __construct(private EntityManager $em)
	{
	}

	/**
	 * @param ValueObjectInterface|ProductValueObject $valueObject
	 * @param BaseEntity|null $entity
	 * @return Product|null
	 */
	public function hydrate(ProductValueObject|ValueObjectInterface $valueObject, ?BaseEntity $entity): ?Product
	{
		if (is_null($entity)) {
			$entity = new Product();
		}

		$entity->setName($valueObject->getName());
		$entity->setDesc($valueObject->getDesc());
		$entity->setImage($valueObject->getImage());
		$entity->setPrice($valueObject->getPrice());
		$entity->setUrl($valueObject->getUrl());

		foreach ($valueObject->getCategories() as $category) {
			$category = $this->em->getRepository(Category::class)->findBy(["id" => $category]);

			if ($category instanceof Category) {
				$entity->addCategory($category);
			}
		}

		return $entity;
	}
}
