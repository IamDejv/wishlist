<?php
declare(strict_types=1);

namespace App\Controller\Pub;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestBody;
use Apitte\Core\Annotation\Controller\Responses;
use Apitte\Core\Annotation\Controller\Response;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Enum\ResponseEnum;
use Exception;
use App\Helpers\ResponseHelper;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use App\Service\UserService;
use App\ValueObject\Exception\InvalidValueException;
use App\ValueObject\UserValueObject;

/**
 * @Path("/")
 */
class LoginController extends BasePubController
{
	public function __construct(private UserService $userService)
	{
	}

	/**
	 * @Path("/sign-up")
	 * @Method("POST")
	 * @Responses({
	 *     @Response(code="200", description="Success")
	 * })
	 * @RequestBody(entity="App\ValueObject\UserValueObject")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function signUp(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			/** @var UserValueObject $userValueObject */
			$userValueObject = $this->getRequestEntity($request);

			$token = $request->getHeader("Authentication");

			$user = $this->userService->signUp($userValueObject, $token[0]);

			return $response
				->withStatus(ResponseHelper::OK)
				->withAttribute(ResponseEnum::SINGLE, $user);
		} catch (Exception|AuthException|FirebaseException|InvalidValueException $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}
}
