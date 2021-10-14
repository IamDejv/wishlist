<?php
declare(strict_types=1);

namespace App\Controller\Pub;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestBody;
use Apitte\Core\Annotation\Controller\Responses;
use Apitte\Core\Annotation\Controller\Response;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Controller\Auth\BaseAuthController;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use App\Helpers\ResponseHelper;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;
use App\Model\Factory\UserFactory;
use Nette\Security\AuthenticationException;
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

			return $response->writeJsonBody($user)
				->withStatus(ResponseHelper::OK);
		} catch (InvalidValueException $e) {
			return $response->writeJsonBody([
				'message' => $e->getMessage(),
				'validation' => $e->getErrors(),
			])->withStatus(ResponseHelper::BAD_REQUEST);
		} catch (Exception|AuthException|FirebaseException $e) {
			return $response->writeJsonBody([
				'message' => $e->getMessage(),
			])->withStatus(ResponseHelper::BAD_REQUEST);
		}
	}
}
