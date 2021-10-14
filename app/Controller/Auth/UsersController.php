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
	 * @Path("/all")
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
	 * @Path("/trainers")
	 * @Method("GET")
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function listTrainers(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response->writeJsonBody($this->userService->getAllByRole([RoleEnum::TRAINER_ID, RoleEnum::MANAGER_ID, RoleEnum::ADMINISTRATOR_ID]))
			->withStatus(ResponseHelper::OK);
	}

	/**
	 * @Path("/")
	 * @Method("GET")
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function listUsers(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response->writeJsonBody($this->userService->getAllByRole([RoleEnum::USER_ID]))
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

	/**
	 * @Path("/role/{userId}")
	 * @Method("PUT")
	 * @RequestParameters({
	 * 		@RequestParameter(name="userId", type="string")
	 *})
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function changeRole(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$userId = $request->getParameter("userId");

			$role = $request->getJsonBody()["role"];

			$user = $this->userService->changeRole($userId, $role);
			return $response->writeJsonBody($user)
				->withStatus(ResponseHelper::OK);
		} catch (EntityNotFoundException $e) {
			return $response->writeJsonBody([
				'message' => $e->getMessage(),
			])->withStatus(ResponseHelper::NOT_FOUND);
		} catch (Exception $e) {
			return $response->writeJsonBody([
				'message' => $e->getMessage(),
			])->withStatus(ResponseHelper::BAD_REQUEST);
		}
	}

	/**
	 * @Path("/{userId}")
	 * @Method("DELETE")
	 * @RequestParameters({
	 * 		@RequestParameter(name="userId", type="string")
	 *})
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function delete(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$userId = $request->getParameter("userId");

			$this->userService->disable($userId);
			return $response->withStatus(ResponseHelper::NO_CONTENT);
		} catch (EntityNotFoundException $e) {
			return $response->writeJsonBody([
				'message' => $e->getMessage(),
			])->withStatus(ResponseHelper::NOT_FOUND);
		} catch (AuthException|Exception|FirebaseException $e) {
			return $response->writeJsonBody([
				'message' => $e->getMessage(),
			])->withStatus(ResponseHelper::BAD_REQUEST);
		}
	}

	/**
	 * @Path("/activate/{userId}")
	 * @Method("PUT")
	 * @RequestParameters({
	 * 		@RequestParameter(name="userId", type="string")
	 *})
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function activate(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$userId = $request->getParameter("userId");

			$this->userService->enable($userId);
			return $response->withStatus(ResponseHelper::CREATED);
		} catch (EntityNotFoundException $e) {
			return $response->writeJsonBody([
				'message' => $e->getMessage(),
			])->withStatus(ResponseHelper::NOT_FOUND);
		} catch (AuthException|Exception|FirebaseException $e) {
			return $response->writeJsonBody([
				'message' => $e->getMessage(),
			])->withStatus(ResponseHelper::BAD_REQUEST);
		}
	}
}
