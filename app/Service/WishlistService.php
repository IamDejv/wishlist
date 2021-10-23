<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Wishlist;
use App\Model\Factory\WishlistFactory;
use App\Model\Hydrator\WishlistHydrator;
use App\Model\Repository\WishlistRepository;
use App\ValueObject\WishlistValueObject;
use Doctrine\ORM\EntityNotFoundException;
use Nettrine\ORM\EntityManagerDecorator;

class WishlistService extends BaseService
{
	public function __construct(
		protected EntityManagerDecorator $em,
		protected WishlistRepository $repository,
		private WishlistHydrator $hydrator,
		private WishlistFactory $factory
	) {
		parent::__construct($this->repository);
	}


	public function getAll(): array
	{
		return $this->repository->findAll();
	}

	/**
	 * @throws EntityNotFoundException
	 */
	public function get(int $id): Wishlist
	{
		$wishlist = $this->repository->find($id);

		if (!$wishlist instanceof Wishlist) {
			throw new EntityNotFoundException();
		}

		return $wishlist;
	}

	/**
	 * @param WishlistValueObject $valueObject
	 * @return Wishlist|null
	 * @throws EntityNotFoundException
	 */
	public function create(WishlistValueObject $valueObject): ?Wishlist
	{
		$wishlist = $this->factory->create($valueObject);

		$this->em->persist($wishlist);
		$this->em->flush();

		return $wishlist;
	}

	/**
	 * @param int $id
	 * @param WishlistValueObject $valueObject
	 * @return Wishlist|null
	 * @throws EntityNotFoundException
	 */
	public function update(int $id, WishlistValueObject $valueObject): ?Wishlist
	{
		$wishlist = $this->repository->find($id);

		if (!$wishlist instanceof Wishlist) {
			throw new EntityNotFoundException();
		}

		$wishlist = $this->hydrator->hydrate($valueObject, $wishlist);

		$this->em->persist($wishlist);
		$this->em->flush();

		return $wishlist;
	}
}
