<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestBody;
use Apitte\Core\Annotation\Controller\Responses;
use Apitte\Core\Annotation\Controller\Response;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Helpers\ResponseHelper;
use App\Model\Factory\UserFactory;
use App\Model\ResponseMapper\UserResponseMapper;
use App\Service\TokenService;
use App\Service\UserService;
use App\ValueObject\Exception\InvalidValueException;
use App\ValueObject\UserValueObject;
use Doctrine\ORM\EntityNotFoundException;
use Exception;

/**
 * @Path("/me")
 */
class MeController extends BaseAuthController
{
	public function __construct(
		private UserService $userService,
		private TokenService $tokenService
	) {}

	/**
	 * @Path("/")
	 * @Method("GET")
	 * @Responses({
	 *     @Response(code="200", description="Success")
	 * })
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function get(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$tokenHeader = $request->getHeader("Authentication");
			$firebaseUid = $this->tokenService->getFirebaseUidFromToken($tokenHeader[0]);
			$user = $this->userService->getById($firebaseUid);

			return $response->writeJsonBody($user->toArray())
				->withStatus(ResponseHelper::OK);
		} catch (EntityNotFoundException $e) {
			return $response->writeJsonBody([
				'message' => "User does not exist",
			])->withStatus(ResponseHelper::NOT_FOUND);
		} catch (Exception $e) {
			return $response->writeJsonBody([
				'message' => $e->getMessage(),
			])->withStatus(ResponseHelper::UNAUTHORIZED);
		}
	}


	/**
	 * @Path("/")
	 * @Method("PUT")
	 * @Responses({
	 *     @Response(code="200", description="Success")
	 * })
	 * @RequestBody(entity="App\ValueObject\UserValueObject")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function update(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			/** @var UserValueObject $userValueObject */
			$userValueObject = $this->getRequestEntity($request);

			$tokenHeader = $request->getHeader("Authentication");
			$firebaseUid = $this->tokenService->getFirebaseUidFromToken($tokenHeader[0]);

			$user = $this->userService->updateMe($firebaseUid, $userValueObject);

			return $response->writeJsonBody($user)
				->withStatus(ResponseHelper::OK);
		} catch (InvalidValueException $e) {
			return $response->writeJsonBody([
				'message' => $e->getMessage(),
				'validation' => $e->getErrors(),
			])->withStatus(ResponseHelper::BAD_REQUEST);
		} catch (Exception $e) {
			return $response->writeJsonBody([
				'message' => $e->getMessage(),
			])->withStatus(ResponseHelper::BAD_REQUEST);
		}
	}
}
