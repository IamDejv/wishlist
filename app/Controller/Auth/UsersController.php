<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestParameters;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Helpers\ResponseHelper;
use App\Security\RoleEnum;
use App\Service\UserService;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\FirebaseException;

/**
 * @Path("/users")
 */
class UsersController extends BaseAuthController
{
	public function __construct(private UserService $userService)
	{
	}

	/**
	 * @Path("/")
	 * @Method("GET")
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function listAll(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response->writeJsonBody($this->userService->getAll())
			->withStatus(ResponseHelper::OK);
	}


	/**
	 * @Path("/{id}")
	 * @Method("GET")
	 * @RequestParameters({
	 * 		@RequestParameter(name="id", type="string")
	 *})
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function get(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response;
	}
}
