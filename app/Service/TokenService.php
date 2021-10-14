<?php
declare(strict_types=1);

namespace App\Service;


use App\Service\Exception\ExpiredTokenException;
use App\Service\Exception\InvalidTokenException;
use DateTime;
use Exception;
use Kreait\Firebase\Contract\Auth;
use Nette\Security\AuthenticationException;
use Nette\Security\SimpleIdentity;
use Nette\Security\User as NetteUser;

class TokenService
{
	private Auth $auth;

	private UserService $userService;

	/**
	 * TokenService constructor.
	 * @param Auth $firebaseAuthenticator
	 * @param UserService $userService
	 */
	public function __construct(Auth $firebaseAuthenticator, UserService $userService)
	{
		$this->auth = $firebaseAuthenticator;
		$this->userService = $userService;
	}

	/**
	 * @param string $token
	 * @return array
	 * @throws ExpiredTokenException
	 * @throws InvalidTokenException
	 */
	public function checkToken(string $token): array {
		$data = $this->auth->verifyIdToken($token);

		if ($data->isExpired(new DateTime())) {
			throw new ExpiredTokenException();
		}
		$firebaseUid = $data->claims()->get('sub');
		try {
			return [
				$this->userService->getById($firebaseUid),
				$data->toString(),
			];
		} catch (Exception $ex) {
			throw new InvalidTokenException();
		}
	}

	/**
	 * @param array $userEntity
	 * @return SimpleIdentity
	 */
	public function createIdentity(array $userEntity): SimpleIdentity
	{
		[$user, $token] = $userEntity;
		$data = [
			'email' => $user->getEmail(),
			'firebaseUid' => $user->getId(),
		];
		return new SimpleIdentity($token, $user->getRole()->getName(), $data);
	}

	/**
	 * @param NetteUser $user
	 * @param array $userEntity
	 * @throws AuthenticationException
	 */
	public function refreshIdentity(NetteUser $user, array $userEntity)
	{
		$user->logout(true);
		$identity = $this->createIdentity($userEntity);
		$user->login($identity);
	}
}
