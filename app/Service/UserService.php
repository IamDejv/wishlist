<?php
declare(strict_types=1);

namespace App\Service;


use App\Model\Entity\User;
use App\Model\Factory\UserFactory;
use App\Model\Hydrator\UserHydrator;
use App\Model\Repository\UserRepository;
use App\Model\ResponseMapper\UserResponseMapper;
use App\Security\RoleEnum;
use App\Service\Exception\ExpiredTokenException;
use App\ValueObject\UserValueObject;
use DateTime;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Firebase\Auth\Token\Exception\ExpiredToken;
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
		private Auth $auth,
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
			throw new Exception("Email byl již použit");
		}

		/** @var User $user */
		$user = $this->factory->create($userValueObject);

		$token = $this->auth->verifyIdToken($token);

		$firebaseUid = $token->claims()->get("sub");

		$user->setId($firebaseUid);

		try {
			$this->em->persist($user);
			$this->em->flush();

			$this->auth->setCustomUserClaims($firebaseUid, ["role" => RoleEnum::USER_ID]);
			return $user;
		} catch (Exception $e) {
			throw new Exception("Uživatel nebyl vytvořen");
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
	 * @param string $token
	 * @throws EntityNotFoundException
	 * @throws ExpiredTokenException
	 */
	public function checkToken(string $token)
	{
		try {
			$verifiedToken = $this->auth->verifyIdToken($token);
			if ($verifiedToken->isExpired(new DateTime())) {
				throw new ExpiredTokenException();
			}
			$firebaseUid = $verifiedToken->claims()->get("sub");

			$this->getById($firebaseUid);

		} catch (EntityNotFoundException $e) {
			throw new EntityNotFoundException();
		} catch (ExpiredTokenException | ExpiredToken $e) {
			throw new ExpiredTokenException();
		}
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
	 * @param array $roles
	 * @return array
	 */
	public function getAllByRole(array $roles): array
	{
		$users = $this->repository->findAllByRole($roles);

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
	 * @return array
	 * @throws EntityNotFoundException
	 */
	public function updateMe(string $firebaseUid, UserValueObject $userValueObject): array
	{
		$user = $this->getById($firebaseUid);

		$updatedUser = $this->hydrator->hydrate($userValueObject, $user);

		$this->em->flush();

		return $this->responseMapper->toArray($updatedUser);
	}

	/**
	 * @param string $userId
	 * @param int $role
	 * @return array
	 * @throws AuthException
	 * @throws EntityNotFoundException
	 * @throws FirebaseException
	 * @throws Exception
	 */
	public function changeRole(string $userId, int $role): array
	{
		$user = $this->getById($userId);

		$this->em->flush();

		return $this->responseMapper->toArray($user);
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

		$this->auth->disableUser($userId);

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

		if (is_null($user)) {
			throw new EntityNotFoundException();
		}

		$this->auth->enableUser($userId);

		$user->setActive(true);

		$this->em->flush();
	}

}
