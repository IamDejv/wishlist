<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestBody;
use Apitte\Core\Annotation\Controller\Responses;
use Apitte\Core\Annotation\Controller\Response;
use Apitte\Core\Annotation\Controller\Tag;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Enum\RequestEnum;
use App\Enum\ResponseEnum;
use App\Helpers\ResponseHelper;
use App\Model\Entity\Wishlist;
use App\Service\GroupService;
use App\Service\ProductService;
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
 * @Tag(name="me")
 */
class MeController extends BaseAuthController
{
	public function __construct(
		private UserService $userService,
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
			$user = $request->getAttribute(RequestEnum::USER);

			return $response
				->withAttribute(ResponseEnum::SINGLE, $user)
				->withStatus(ResponseHelper::OK);
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
			$user = $request->getAttribute(RequestEnum::USER);

			$products = $this->productService->getProductsFromUserActiveWishlist($user->getId());

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

			$firebaseUid = $request->getAttribute(RequestEnum::USER)->getId();

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
			$user = $request->getAttribute(RequestEnum::USER);

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
			$user = $request->getAttribute(RequestEnum::USER);

			$friends = $this->userService->getUserFriends($user->getId());

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
			$user = $request->getAttribute(RequestEnum::USER);

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
			/** @var ActionWishlistValueObject $valueObject */
			$valueObject = $this->getRequestEntity($request);
			$user = $request->getAttribute(RequestEnum::USER);

			$wishlist = $this->userService->actionWishlist($user->getId(), $valueObject);

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
			$user = $request->getAttribute(RequestEnum::USER);

			/** @var ActionGroupValueObject $valueObject */
			$valueObject = $this->getRequestEntity($request);

			$group = $this->userService->actionGroup($user->getId(), $valueObject);

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
			$user = $request->getAttribute(RequestEnum::USER);

			$friend = $this->userService->actionFriend($user->getId(), $actionFriendValueObject);
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
			$user = $request->getAttribute(RequestEnum::USER);

			$wishlist = $this->wishlistService->getActiveByUser($user->getId());

			if (!$wishlist instanceof Wishlist) {
				$wishlistVo = new WishlistValueObject();
				$wishlistVo->user = $user->getId();
				$wishlistVo->name = $user->getFirstname() . "'s Wishlist";
				$wishlistVo->image = "/public/assets/christmas-1.jpg";
				$wishlist = $this->wishlistService->create($wishlistVo, $user);
				$wishlist->setActive(true);
			}

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
			$owner = $request->getAttribute(RequestEnum::USER);

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
		/** @var WishlistValueObject $valueObject */
		$valueObject = $this->getRequestEntity($request);
		$owner = $request->getAttribute(RequestEnum::USER);

		$wishlist = $this->wishlistService->create($valueObject, $owner);

		return $response
			->withAttribute(ResponseEnum::SINGLE, $wishlist)
			->withStatus(ResponseHelper::OK);
	}

	/**
	 * @Path("/friends/pending")
	 * @Method("GET")
	 * @Responses({
	 *     @Response(code="200", description="Success")
	 * })
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function getPendingFriends(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$user = $request->getAttribute(RequestEnum::USER);

		$users = $this->userService->getPendingFriends($user);

		return $response
			->withAttribute(ResponseEnum::MULTIPLE, $users)
			->withStatus(ResponseHelper::OK);
	}
}
