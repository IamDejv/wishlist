<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestBody;
use Apitte\Core\Annotation\Controller\Responses;
use Apitte\Core\Annotation\Controller\Response;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Enum\ResponseEnum;
use App\Helpers\ResponseHelper;
use App\Service\GroupService;
use App\Service\ProductService;
use App\Service\TokenService;
use App\Service\UserService;
use App\Service\WishlistService;
use App\ValueObject\ActionFriendValueObject;
use App\ValueObject\ActionGroupValueObject;
use App\ValueObject\ActionWishlistValueObject;
use App\ValueObject\GroupValueObject;
use App\ValueObject\ProductValueObject;
use App\ValueObject\UserValueObject;
use App\ValueObject\WishlistValueObject;
use Doctrine\ORM\EntityNotFoundException;
use Exception;

/**
 * @Path("/me")
 */
class MeController extends BaseAuthController
{
	public function __construct(
		private UserService $userService,
		private TokenService $tokenService,
		private ProductService $productService,
		private WishlistService $wishlistService,
		private GroupService $groupService
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

			return $response
				->withAttribute(ResponseEnum::SINGLE, $user)
				->withStatus(ResponseHelper::OK);
		} catch (EntityNotFoundException $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::NOT_FOUND, $e);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::UNAUTHORIZED, $e);
		}
	}

	/**
	 * @Path("/products")
	 * @Method("GET")
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
			$tokenHeader = $request->getHeader("Authentication");
			$firebaseUid = $this->tokenService->getFirebaseUidFromToken($tokenHeader[0]);

			$products = $this->productService->getProductsFromUserActiveWishlist($firebaseUid);

			return $response
				->withAttribute(ResponseEnum::MULTIPLE, $products)
				->withStatus(ResponseHelper::OK);
		} catch (EntityNotFoundException $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::NOT_FOUND, $e);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::UNAUTHORIZED, $e);
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

			return $response
				->withAttribute(ResponseEnum::SINGLE, $user)
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/groups")
	 * @Method("GET")
	 * @Responses({
	 *     @Response(code="200", description="Success")
	 * })
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function listGroups(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$tokenHeader = $request->getHeader("Authentication");
			$firebaseUid = $this->tokenService->getFirebaseUidFromToken($tokenHeader[0]);
			$user = $this->userService->getById($firebaseUid);

			return $response
				->withAttribute(ResponseEnum::MULTIPLE, $user->getGroups())
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/friends")
	 * @Method("GET")
	 * @Responses({
	 *     @Response(code="200", description="Success")
	 * })
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function listFriends(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$tokenHeader = $request->getHeader("Authentication");
			$firebaseUid = $this->tokenService->getFirebaseUidFromToken($tokenHeader[0]);

			$friends = $this->userService->getUserFriends($firebaseUid);

			return $response
				->withAttribute(ResponseEnum::MULTIPLE, $friends)
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/wishlists")
	 * @Method("GET")
	 * @Responses({
	 *     @Response(code="200", description="Success")
	 * })
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function listWishlists(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$tokenHeader = $request->getHeader("Authentication");
			$firebaseUid = $this->tokenService->getFirebaseUidFromToken($tokenHeader[0]);
			$user = $this->userService->getById($firebaseUid);

			return $response
				->withAttribute(ResponseEnum::MULTIPLE, $user->getWishlists())
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/wishlists/active")
	 * @Method("PUT")
	 * @Responses({
	 *     @Response(code="200", description="Success")
	 * })
	 * @RequestBody(entity="App\ValueObject\ActionWishlistValueObject")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function setWishlistActive(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$tokenHeader = $request->getHeader("Authentication");
			$firebaseUid = $this->tokenService->getFirebaseUidFromToken($tokenHeader[0]);

			/** @var ActionWishlistValueObject $valueObject */
			$valueObject = $this->getRequestEntity($request);

			$wishlist = $this->userService->actionWishlist($firebaseUid, $valueObject);

			return $response
				->withAttribute(ResponseEnum::SINGLE, $wishlist)
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/groups/active")
	 * @Method("PUT")
	 * @Responses({
	 *     @Response(code="200", description="Success")
	 * })
	 * @RequestBody(entity="App\ValueObject\ActionGroupValueObject")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function setGroupActive(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$tokenHeader = $request->getHeader("Authentication");
			$firebaseUid = $this->tokenService->getFirebaseUidFromToken($tokenHeader[0]);

			/** @var ActionGroupValueObject $valueObject */
			$valueObject = $this->getRequestEntity($request);

			$group = $this->userService->actionGroup($firebaseUid, $valueObject);

			return $response
				->withAttribute(ResponseEnum::SINGLE, $group)
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/friends")
	 * @Method("PUT")
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
			$tokenHeader = $request->getHeader("Authentication");
			$firebaseUid = $this->tokenService->getFirebaseUidFromToken($tokenHeader[0]);

			$friend = $this->userService->actionFriend($firebaseUid, $actionFriendValueObject);
			return $response
				->withAttribute(ResponseEnum::SINGLE, $friend)
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/products")
	 * @Method("POST")
	 * @Responses({
	 * 		@Response(code="200", description="Success")
	 * })
	 * @RequestBody(entity="App\ValueObject\ProductValueObject")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function addToActiveWishlist(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			/** @var ProductValueObject $valueObject */
			$valueObject = $this->getRequestEntity($request);
			$tokenHeader = $request->getHeader("Authentication");
			$firebaseUid = $this->tokenService->getFirebaseUidFromToken($tokenHeader[0]);

			$wishlist = $this->wishlistService->getActiveByUser($firebaseUid);

			$valueObject->setWishlistId($wishlist->getId());

			$product = $this->productService->create($valueObject);

			return $response
				->withAttribute(ResponseEnum::SINGLE, $product)
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/groups")
	 * @Method("POST")
	 * @Responses({
	 * 		@Response(code="200", description="Success")
	 * })
	 * @RequestBody(entity="App\ValueObject\GroupValueObject")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function createGroup(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			/** @var GroupValueObject $valueObject */
			$valueObject = $this->getRequestEntity($request);
			$tokenHeader = $request->getHeader("Authentication");
			$firebaseUid = $this->tokenService->getFirebaseUidFromToken($tokenHeader[0]);
			$owner = $this->userService->getById($firebaseUid);

			$group = $this->groupService->create($valueObject, $owner);

			return $response
				->withAttribute(ResponseEnum::SINGLE, $group)
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/wishlists")
	 * @Method("POST")
	 * @Responses({
	 * 		@Response(code="200", description="Success")
	 * })
	 * @RequestBody(entity="App\ValueObject\WishlistValueObject")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function create(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			/** @var WishlistValueObject $valueObject */
			$valueObject = $this->getRequestEntity($request);
			$tokenHeader = $request->getHeader("Authentication");
			$firebaseUid = $this->tokenService->getFirebaseUidFromToken($tokenHeader[0]);
			$owner = $this->userService->getById($firebaseUid);

			$wishlist = $this->wishlistService->create($valueObject, $owner);

			return $response
				->withAttribute(ResponseEnum::SINGLE, $wishlist)
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}
}
