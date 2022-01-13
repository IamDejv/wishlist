<?php
declare(strict_types=1);

namespace App\Service;

use Apitte\Core\Exception\Api\ClientErrorException;
use App\Helpers\ResponseHelper;
use App\Model\Entity\Group;
use App\Model\Entity\User;
use App\Model\Factory\GroupFactory;
use App\Model\Hydrator\GroupHydrator;
use App\Model\Repository\GroupRepository;
use App\ValueObject\ActionUserValueObject;
use App\ValueObject\GroupValueObject;
use Doctrine\ORM\EntityNotFoundException;
use Nettrine\ORM\EntityManagerDecorator;

class GroupService extends BaseService
{
	public function __construct(
		protected EntityManagerDecorator $em,
		protected GroupRepository $repository,
		private GroupHydrator $hydrator,
		private GroupFactory $factory,
		private UserService $userService
	) {
		parent::__construct($this->repository);
	}


	public function getAll(): array
	{
		return $this->repository->findAll();
	}

	public function get(int $id): Group
	{
		$group = $this->repository->find($id);

		if (!$group instanceof Group) {
			throw new ClientErrorException("Group not found", ResponseHelper::NOT_FOUND);
		}

		return $group;
	}

	/**
	 * @param GroupValueObject $valueObject
	 * @param User|null $owner
	 * @return Group|null
	 */
	public function create(GroupValueObject $valueObject, User $owner = null): ?Group
	{
		$group = $this->factory->create($valueObject);

		$group->setOwner($owner);
		$owner->addGroup($group);

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

	/**
	 * @param int $id
	 * @return array
	 */
	public function getGroupUsers(int $id): array
	{
		return $this->em
			->getRepository(User::class)
			->getGroupUsers($id);
	}

	/**
	 * @param int $id
	 * @param ActionUserValueObject $valueObject
	 * @return User
	 * @throws EntityNotFoundException
	 */
	public function actionUser(int $id, ActionUserValueObject $valueObject): User
	{
		$group = $this->get($id);

		$user = $this->userService->getById($valueObject->getId());

		if ($valueObject->getAction() === "addToGroup") {
			$user->addGroup($group);
		} else if ($valueObject->getAction() === "removeFromGroup") {
			$user->removeGroup($group);
		}

		$this->em->flush();

		return $user;
	}

	public function archive(int $id, User $user)
	{
		$wishlist = $this->get($id);

		if ($wishlist->getOwner()->getId() !== $user->getId()) {
			throw new ClientErrorException("Not your group", ResponseHelper::BAD_REQUEST);
		}

		$wishlist->setArchived(true);

		$this->em->flush();
	}
}
