<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Category;
use App\Model\Factory\CategoryFactory;
use App\Model\Hydrator\CategoryHydrator;
use App\Model\Repository\CategoryRepository;
use App\ValueObject\CategoryValueObject;
use Doctrine\ORM\EntityNotFoundException;
use Nettrine\ORM\EntityManagerDecorator;

class CategoryService extends BaseService
{
	public function __construct(
		protected EntityManagerDecorator $em,
		protected CategoryRepository $repository,
		private CategoryHydrator $hydrator,
		private CategoryFactory $factory
	) {
		parent::__construct($this->repository);
	}


	public function getAll(): array
	{
		return $this->repository->findAll();
	}

	/**
	 * @param int $id
	 * @return Category
	 * @throws EntityNotFoundException
	 */
	public function get(int $id): Category
	{
		$category = $this->repository->find($id);

		if (!$category instanceof Category) {
			throw new EntityNotFoundException();
		}

		return $category;
	}

	public function create(CategoryValueObject $valueObject): ?Category
	{
		$category = $this->factory->create($valueObject);

		$this->em->persist($category);
		$this->em->flush();

		return $category;
	}

	/**
	 * @param int $id
	 * @param CategoryValueObject $valueObject
	 * @return Category|null
	 * @throws EntityNotFoundException
	 */
	public function update(int $id, CategoryValueObject $valueObject): ?Category
	{
		$category = $this->repository->find($id);

		if (!$category instanceof Category) {
			throw new EntityNotFoundException();
		}

		$category = $this->hydrator->hydrate($valueObject, $category);

		$this->em->persist($category);
		$this->em->flush();

		return $category;
	}
}
