<?php
declare(strict_types=1);

namespace App\Controller;

use App\Helpers\ResponseHelper;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\UI\Controller\IController;
use Exception;
use Nette\Security\User;
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
	 * @param ApiRequest $request
	 * @param bool $validate
	 * @return ValueObject
	 */
	public function getRequestEntity(ApiRequest $request, bool $validate = true): ValueObject
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
