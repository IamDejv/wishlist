<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use App\Controller\BaseV1Controller;
use App\Helpers\ResponseHelper;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Nette\Security\AuthenticationException;


abstract class BaseAuthController extends BaseV1Controller
{
	/**
	 * @param ApiRequest $apiRequest
	 * @return string
	 * @throws AuthenticationException
	 * @throws IdentityNotFoundException
	 * @throws TokenException
	 */
	public function getLoggedUserId(ApiRequest $apiRequest): string
	{
		$userData = $this->getUser($apiRequest)->getIdentity()->getData();
		if (!isset($userData['firebaseUid'])) {
			throw new IdentityNotFoundException('Authentication error!');
		}

		return $userData['firebaseUid'];
	}
}
