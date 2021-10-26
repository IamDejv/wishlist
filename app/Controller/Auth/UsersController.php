<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestBody;
use Apitte\Core\Annotation\Controller\RequestParameters;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Annotation\Controller\Tag;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Enum\RequestEnum;
use App\Enum\ResponseEnum;
use App\Helpers\ResponseHelper;
use App\Service\TokenService;
use App\Service\UserService;
use App\ValueObject\AddFriendValueObject;
use Exception;

/**
 * @Path("/users")
 */
class UsersController extends BaseAuthController
{
	public function __construct(private UserService $service, private TokenService $tokenService)
	{
	}

	/**
	 * @Path("/")
	 * @Method("GET")
	 * @RequestParameters({
	 * 		@RequestParameter(name="searchValue", type="string", in="query", required=false)
	 *})
	 * @Tag(name="pagination")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function listAll(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$paginator = $request->getAttribute(RequestEnum::PAGINATION);
		$search = $request->getParameter(RequestEnum::SEARCH);

		$users = $this->service->getAll($search, $paginator);

		return $response
			->withAttribute(ResponseEnum::MULTIPLE, $users);
	}

	/**
	 * @Path("/new")
	 * @Method("GET")
	 * @RequestParameters({
	 * 		@RequestParameter(name="searchValue", type="string", in="query", required=false)
	 *})
	 * @Tag(name="pagination")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function listNewFriends(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$tokenHeader = $request->getHeader("Authentication");
		$firebaseUid = $this->tokenService->getFirebaseUidFromToken($tokenHeader[0]);

		$paginator = $request->getAttribute(RequestEnum::PAGINATION);
		$search = $request->getParameter(RequestEnum::SEARCH);

		$users = $this->service->getNotUserFriend($firebaseUid, $search, $paginator);

		return $response
			->withAttribute(ResponseEnum::MULTIPLE, $users);
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
	 * @Path("/{id}/friends")
	 * @Method("GET")
	 * @RequestParameters({
	 * 		@RequestParameter(name="id", type="string")
	 *})
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function getFriends(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$id = $request->getParameter('id');

			$friends = $this->service->getUserFriends($id);
			return $response
				->withAttribute(ResponseEnum::MULTIPLE, $friends);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/{id}/friends")
	 * @Method("PUT")
	 * @RequestParameters({
	 * 		@RequestParameter(name="id", type="string")
	 *})
	 *
	 * @RequestBody(entity="App\ValueObject\AddFriendValueObject")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function addFriend(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			/** @var AddFriendValueObject $addFriendValueObject */
			$addFriendValueObject = $this->getRequestEntity($request);

			$id = $request->getParameter('id');

			$this->service->addFriend($id, $addFriendValueObject);
			return $response
				->withStatus(ResponseHelper::NO_CONTENT);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}
}
