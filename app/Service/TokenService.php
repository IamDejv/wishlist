<?php
declare(strict_types=1);

namespace App\Service;


use App\Model\Entity\User;
use App\Service\Exceptions\ExpiredTokenException;
use DateTime;
use Doctrine\ORM\EntityNotFoundException;
use Firebase\Auth\Token\Exception\ExpiredToken;
use Kreait\Firebase\Contract\Auth;

class TokenService
{
	public function __construct(private Auth $firebaseAuthenticator, private UserService $userService)
	{
	}

	/**
	 * @param string $token
	 * @param bool $withUser
	 * @return User|null
	 * @throws EntityNotFoundException
	 * @throws ExpiredTokenException
	 */
	public function checkToken(string $token, bool $withUser = false): ?User
	{
		try {
			$verifiedToken = $this->firebaseAuthenticator->verifyIdToken($token);

			if ($verifiedToken->isExpired(new DateTime())) {
				throw new ExpiredTokenException();
			}

			if ($withUser) {
				$firebaseUid = $verifiedToken->claims()->get("sub");
				return $this->userService->getById($firebaseUid);
			}

			return null;
		} catch (EntityNotFoundException $e) {
			throw new EntityNotFoundException();
		} catch (ExpiredTokenException | ExpiredToken $e) {
			throw new ExpiredTokenException();
		}
	}

	public function getFirebaseUidFromToken(string $token): string
	{
		$verifiedToken = $this->firebaseAuthenticator->verifyIdToken($token);

		return $verifiedToken->claims()->get("sub");
	}
}
