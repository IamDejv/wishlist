<?php
declare(strict_types=1);

namespace App\Service;


use App\Model\Entity\User;
use App\Model\Factory\UserFactory;
use App\Model\Hydrator\UserHydrator;
use App\Model\Repository\UserRepository;
use App\Model\ResponseMapper\UserResponseMapper;
use App\ValueObject\UserValueObject;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use Nettrine\ORM\EntityManagerDecorator;

class UserService extends BaseService
{
	public function __construct(
		protected EntityManagerDecorator $em,
		protected UserRepository $repository,
		private UserResponseMapper $responseMapper,
		private UserHydrator $hydrator,
		private Auth $firebaseAuthenticator,
		private UserFactory $factory
	) {
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
	 * @param int $id
	 * @param UserValueObject $userValueObject
	 * @return User
	 * @throws EntityNotFoundException
	 */
	public function update(int $id, UserValueObject $userValueObject): User
	{
		/** @var User $user */
		$user = $this->repository->find($id);

		if (is_null($user)) {
			throw new EntityNotFoundException("Tento uživatel neexistuje");
		}

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
	 * @return array
	 */
	public function getAll(): array
	{
		$users = $this->repository->findAll();

		$userArray = [];
		foreach ($users as $user) {
			array_push($userArray, $this->responseMapper->toArray($user));
		}
		return $userArray;
	}

	/**
	 * @param string $firebaseUid
	 * @return User
	 * @throws EntityNotFoundException
	 */
	public function getById(string $firebaseUid): User
	{
		/** @var User $user */
		$user = $this->repository->find($firebaseUid);
		if (is_null($user)) {
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

		if ($user instanceof User) {
			throw new EntityNotFoundException();
		}

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

		if ($user instanceof User) {
			throw new EntityNotFoundException();
		}

		$this->firebaseAuthenticator->disableUser($userId);

		$this->em->flush();
	}

	/**
	 * @param string $userId
	 * @throws AuthException
	 * @throws EntityNotFoundException
	 * @throws FirebaseException
	 */
	public function enable(string $userId)
	{
		$user = $this->repository->find($userId);

		if ($user instanceof User) {
			throw new EntityNotFoundException();
		}

		$this->firebaseAuthenticator->enableUser($userId);

		$this->em->flush();
	}

}
