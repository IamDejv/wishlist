<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Product;
use App\Model\Factory\ProductFactory;
use App\Model\Hydrator\ProductHydrator;
use App\Model\Repository\ProductRepository;
use App\ValueObject\ProductValueObject;
use Doctrine\ORM\EntityNotFoundException;
use Nettrine\ORM\EntityManagerDecorator;

class ProductService extends BaseService
{
	public function __construct(
		protected EntityManagerDecorator $em,
		protected ProductRepository $repository,
		private ProductHydrator $hydrator,
		private ProductFactory $factory
	) {
		parent::__construct($this->repository);
	}


	public function getAll(): array
	{
		return $this->repository->findAll();
	}

	/**
	 * @param int $id
	 * @return Product
	 * @throws EntityNotFoundException
	 */
	public function get(int $id): Product
	{
		$product = $this->repository->find($id);

		if (!$product instanceof Product) {
			throw new EntityNotFoundException();
		}

		return $product;
	}

	public function create(ProductValueObject $valueObject): ?Product
	{
		$product = $this->factory->create($valueObject);

		$this->em->persist($product);
		$this->em->flush();

		return $product;
	}

	/**
	 * @param int $id
	 * @param ProductValueObject $valueObject
	 * @return Product|null
	 * @throws EntityNotFoundException
	 */
	public function update(int $id, ProductValueObject $valueObject): ?Product
	{
		$product = $this->repository->find($id);

		if (!$product instanceof Product) {
			throw new EntityNotFoundException();
		}

		$product = $this->hydrator->hydrate($valueObject, $product);

		$this->em->persist($product);
		$this->em->flush();

		return $product;
	}
}
