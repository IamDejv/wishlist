<?php
declare(strict_types=1);

namespace App\Service;


use App\Model\Entity\User;
use App\Model\Factory\UserFactory;
use App\Model\Hydrator\UserHydrator;
use App\Model\Repository\FriendRepository;
use App\Model\Repository\UserRepository;
use App\ValueObject\AddFriendValueObject;
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
		private FriendRepository         $friendRepository
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
		$myFriends = $this->friendRepository->findFriends($id);
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
	 * @return ArrayCollection|Collection
	 * @throws EntityNotFoundException
	 */
	public function getUserFriends(string $id): ArrayCollection|Collection
	{
		$user = $this->getById($id);

		return $user->getMyFriends();
	}

	/**
	 * @throws EntityNotFoundException
	 */
	public function addFriend(string $id, AddFriendValueObject $valueObject)
	{
		$newFriend = $this->getById($valueObject->getId());

		$user = $this->getById($id);

		$user->addFriend($newFriend);

		$this->em->flush();
	}
}
