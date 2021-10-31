<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestBody;
use Apitte\Core\Annotation\Controller\RequestParameters;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Annotation\Controller\Responses;
use Apitte\Core\Annotation\Controller\Response;
use Apitte\Core\Annotation\Controller\Tag;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Enum\RequestEnum;
use App\Enum\ResponseEnum;
use App\Helpers\ResponseHelper;
use App\Service\ProductService;
use App\Service\TokenService;
use App\Service\UserService;
use App\ValueObject\ActionFriendValueObject;
use Doctrine\ORM\EntityNotFoundException;
use Exception;

/**
 * @Path("/users")
 */
class UsersController extends BaseAuthController
{
	public function __construct(
		private UserService    $service,
		private TokenService   $tokenService,
		private ProductService $productService)
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
	 * @RequestBody(entity="App\ValueObject\ActionFriendValueObject")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function actionFriend(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			/** @var ActionFriendValueObject $actionFriendValueObject */
			$actionFriendValueObject = $this->getRequestEntity($request);

			$id = $request->getParameter('id');

			$friend = $this->service->actionFriend($id, $actionFriendValueObject);
			return $response
				->withAttribute(ResponseEnum::SINGLE, $friend)
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/{id}/products")
	 * @Method("GET")
	 * @RequestParameters({
	 * 		@RequestParameter(name="id", type="string")
	 *})
	 *
	 * @Responses({
	 *     @Response(code="200", description="Success")
	 * })
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function getProducts(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$id = $request->getParameter('id');

			$products = $this->productService->getProductsFromUserActiveWishlist($id);

			return $response
				->withAttribute(ResponseEnum::MULTIPLE, $products)
				->withStatus(ResponseHelper::OK);
		} catch (EntityNotFoundException $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::NOT_FOUND, $e);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::UNAUTHORIZED, $e);
		}
	}

}
