<?php
declare(strict_types=1);

namespace App\Controller;

use App\HandlerExtension\Exception\TokenException;
use App\Helpers\ResponseHelper;
use App\Security\AuthorizatorFactory;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\UI\Controller\IController;
use Exception;
use Nette\Security\AuthenticationException;
use Nette\Security\User;
use App\Service\TokenService;
use App\ValueObject\Exception\InvalidValueException;
use App\ValueObject\ValueObject;

/**
 * @Path("/api/v1")
 */
abstract class BaseV1Controller implements IController
{
	/**
	 * @inject
	 */
	public User $user;

	/**
	 * @inject
	 */
	public TokenService $tokenService;

	/**
	 * @param ApiRequest|null $request
	 * @return User
	 * @throws TokenException
	 * @throws AuthenticationException
	 */
	public function getUser(ApiRequest $request = null): User
	{
		$this->user->setAuthorizator(AuthorizatorFactory::create());
		if (empty($request) === false) {
			$tokenHeaderValue = $request->getHeader('Authentication');
			$this->tokenService->refreshIdentity(
				$this->user,
				$this->tokenService->checkToken($tokenHeaderValue[0])
			);
		}
		return $this->user;
	}

	/**
	 * @param ApiRequest $request
	 * @param bool $validate
	 * @return ValueObject
	 * @throws InvalidValueException
	 */
	public function getRequestEntity(ApiRequest $request, $validate = true): ValueObject
	{

		/** @var ValueObject $entity */
		$entity = $request->getEntity()
			->fromRequest($request);

		if ($validate) {
			$entity->validate();
		}

		return $entity;
	}

	/**
	 * @param ApiResponse $response
	 * @param Exception $exception
	 * @param int $code
	 * @return ApiResponse
	 */
	protected function returnException(ApiResponse $response, Exception $exception, int $code = ResponseHelper::BAD_REQUEST): ApiResponse
	{
		return $response
			->writeJsonBody(ResponseHelper::formatException($exception))
			->withStatus($code);
	}
}
