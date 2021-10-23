<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Entity\Group;
use App\Model\Factory\GroupFactory;
use App\Model\Hydrator\GroupHydrator;
use App\Model\Repository\GroupRepository;
use App\ValueObject\GroupValueObject;
use Doctrine\ORM\EntityNotFoundException;
use Nettrine\ORM\EntityManagerDecorator;

class GroupService extends BaseService
{
	public function __construct(
		protected EntityManagerDecorator $em,
		protected GroupRepository $repository,
		private GroupHydrator $hydrator,
		private GroupFactory $factory
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
	public function get(int $id): Group
	{
		$group = $this->repository->find($id);

		if (!$group instanceof Group) {
			throw new EntityNotFoundException();
		}

		return $group;
	}

	public function create(GroupValueObject $valueObject): ?Group
	{
		$group = $this->factory->create($valueObject);

		$this->em->persist($group);
		$this->em->flush();

		return $group;
	}

	/**
	 * @param int $id
	 * @param GroupValueObject $valueObject
	 * @return Group|null
	 * @throws EntityNotFoundException
	 */
	public function update(int $id, GroupValueObject $valueObject): ?Group
	{
		$group = $this->repository->find($id);

		if (!$group instanceof Group) {
			throw new EntityNotFoundException();
		}

		$group = $this->hydrator->hydrate($valueObject, $group);

		$this->em->persist($group);
		$this->em->flush();

		return $group;
	}
}
