<?php
declare(strict_types=1);

namespace App\Service;


use App\Model\Entity\Group;
use App\Model\Entity\User;
use App\Model\Entity\Wishlist;
use App\Model\Factory\UserFactory;
use App\Model\Hydrator\UserHydrator;
use App\Model\Repository\UserRepository;
use App\ValueObject\ActionFriendValueObject;
use App\ValueObject\ActionGroupValueObject;
use App\ValueObject\ActionWishlistValueObject;
use App\ValueObject\AddFriendValueObject;
use App\ValueObject\RemoveFriendValueObject;
use App\ValueObject\UserValueObject;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\ExpressionBuilder;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Nette\Utils\Paginator;
use Nettrine\ORM\EntityManagerDecorator;

class UserService extends BaseService
{
	public function __construct(
		protected EntityManagerDecorator $em,
		protected UserRepository         $repository,
		private UserHydrator             $hydrator,
		private Auth                     $firebaseAuthenticator,
		private UserFactory              $factory,
	)
	{
		parent::__construct($this->repository);
	}

	/**
	 * @param UserValueObject $userValueObject
	 * @param string $token
	 * @return User
	 *
	 * @throws AuthException
	 * @throws FirebaseException
	 * @throws Exception
	 */
	public function signUp(UserValueObject $userValueObject, string $token): User
	{
		$existingUser = $this->findByEmail($userValueObject->getEmail());

		if (!is_null($existingUser)) {
			throw new Exception("Email is already used");
		}

		/** @var User $user */
		$user = $this->factory->create($userValueObject);

		$token = $this->firebaseAuthenticator->verifyIdToken($token);

		$firebaseUid = $token->claims()->get("sub");

		$user->setId($firebaseUid);

		try {
			$this->em->persist($user);
			$this->em->flush();

			return $user;
		} catch (Exception $e) {
			throw new Exception("User not created");
		}
	}

	/**
	 * @param string $id
	 * @param UserValueObject $userValueObject
	 * @return User
	 * @throws EntityNotFoundException
	 */
	public function update(string $id, UserValueObject $userValueObject): User
	{
		$user = $this->getById($id);

		$updatedUser = $this->hydrator->hydrate($userValueObject, $user);

		$this->em->flush();

		return $updatedUser;
	}

	/**
	 * @param string $email
	 * @return User|null
	 */
	public function findByEmail(string $email): ?User
	{
		return $this->repository->findOneBy(["email" => $email]);
	}

	/**
	 * @param string|null $search
	 * @param Paginator $paginator
	 * @return array
	 */
	public function getAll(?string $search, Paginator $paginator): array
	{
		$criteria = new Criteria();
		if (isset($search)) {
			$criteria
				->where((new ExpressionBuilder())->contains("firstname", $search))
				->orWhere((new ExpressionBuilder())->contains("lastname", $search))
			;
		}

		$users = $this->repository->findByCriteria($criteria, [], $paginator->getItemsPerPage(), $paginator->getOffset());

		return $users;
	}

	/**
	 * @param string $id
	 * @param string|null $search
	 * @param Paginator $paginator
	 * @return array
	 */
	public function getNotUserFriend(string $id, ?string $search, Paginator $paginator): array
	{
		$myFriends = $this->repository->findFriends($id);
		$users = $this->repository->findUsers($id, $search, $myFriends, [], $paginator->getItemsPerPage(), $paginator->getOffset());

		return $users;
	}

	/**
	 * @param string $firebaseUid
	 * @return User
	 * @throws EntityNotFoundException
	 */
	public function getById(string $firebaseUid): User
	{
		$user = $this->repository->find($firebaseUid);

		if (!$user instanceof User) {
			throw new EntityNotFoundException();
		}

		return $user;
	}

	/**
	 * @param string $firebaseUid
	 * @param UserValueObject $userValueObject
	 * @return User
	 * @throws EntityNotFoundException
	 */
	public function updateMe(string $firebaseUid, UserValueObject $userValueObject): User
	{
		$user = $this->getById($firebaseUid);

		$updatedUser = $this->hydrator->hydrate($userValueObject, $user);

		$this->em->flush();

		return $updatedUser;
	}

	/**
	 * @param string $userId
	 * @throws AuthException
	 * @throws EntityNotFoundException
	 * @throws FirebaseException
	 */
	public function disable(string $userId)
	{
		$user = $this->getById($userId);

		if (!$user instanceof User) {
			throw new EntityNotFoundException();
		}

		$this->firebaseAuthenticator->disableUser($userId);

		$this->em->flush();
	}

	/**
	 * @param string $id
	 * @throws AuthException
	 * @throws EntityNotFoundException
	 * @throws FirebaseException
	 */
	public function enable(string $id)
	{
		$this->getById($id);

		$this->firebaseAuthenticator->enableUser($id);

		$this->em->flush();
	}

	/**
	 * @param string $id
	 * @return array
	 */
	public function getUserFriends(string $id): array
	{
		return $this->repository->getUserFriends($id);
	}

	/**
	 * @throws EntityNotFoundException
	 */
	public function addFriend(string $id, string $friendId): User
	{
		$newFriend = $this->getById($friendId);

		$user = $this->getById($id);

		$user->addFriend($newFriend);

		$this->em->flush();

		return $newFriend;
	}

	/**
	 * @throws EntityNotFoundException
	 */
	public function removeFriend(string $id, string $friendId): User
	{
		$newFriend = $this->getById($friendId);

		$user = $this->getById($id);

		$user->removeFriend($newFriend);

		$this->em->flush();

		return $newFriend;
	}

	public function actionFriend(string $id, ActionFriendValueObject $actionFriendValueObject): ?User
	{
		$action = $actionFriendValueObject->getAction();
		if ($action === "remove") {
			return $this->removeFriend($id, $actionFriendValueObject->getId());
		} else if ($action === "add") {
			return $this->addFriend($id, $actionFriendValueObject->getId());
		}

		return null;
	}

	/**
	 * @throws EntityNotFoundException
	 */
	public function actionWishlist(string $id, ActionWishlistValueObject $valueObject): Wishlist
	{
		$user = $this->getById($id);

		$updatedWishlist = $this->em->getRepository(Wishlist::class)->find($valueObject->getId());

		if (!$updatedWishlist instanceof Wishlist) {
			throw new EntityNotFoundException("Wishlist not found");
		}

		if ($valueObject->getAction() === "setActive") {
			foreach ($user->getWishlists() as $wishlist) {
				if ($wishlist->getId() === $valueObject->getId()) {
					$wishlist->setActive(true);
				} else {
					$wishlist->setActive(false);
				}
			}
		} else if ($valueObject->getAction() === "setInactive") {
			foreach ($user->getWishlists() as $wishlist) {
				$wishlist->setActive(false);
			}
		}

		$this->em->flush();

		return $updatedWishlist;
	}

	/**
	 * @param string $id
	 * @param ActionGroupValueObject $valueObject
	 * @return Group
	 * @throws EntityNotFoundException
	 */
	public function actionGroup(string $id, ActionGroupValueObject $valueObject): Group
	{
		$user = $this->getById($id);

		$updatedGroup = $this->em->getRepository(Group::class)->find($valueObject->getId());

		if (!$updatedGroup instanceof Group) {
			throw new EntityNotFoundException("Group not found");
		}

		if ($valueObject->getAction() === "setActive") {
			foreach ($user->getGroups() as $group) {
				if ($group->getId() === $valueObject->getId()) {
					$group->setActive(true);
				} else {
					$group->setActive(false);
				}
			}
		} else if ($valueObject->getAction() === "setInactive") {
			foreach ($user->getGroups() as $group) {
				$group->setActive(false);
			}
		}

		$this->em->flush();

		return $updatedGroup;
	}
}
