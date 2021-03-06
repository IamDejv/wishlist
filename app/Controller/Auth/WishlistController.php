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
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\RequestAttributes;
use App\Enum\RequestEnum;
use App\Enum\ResponseEnum;
use App\Helpers\ResponseHelper;
use App\Service\WishlistService;
use App\ValueObject\WishlistValueObject;
use Doctrine\ORM\EntityNotFoundException;
use Exception;

/**
 * @Path("/wishlists")
 */
class WishlistController extends BaseAuthController
{
	public function __construct(private WishlistService $service)
	{
	}

	/**
	 * @Path("/")
	 * @Method("GET")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function listAll(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response
			->withAttribute(ResponseEnum::MULTIPLE, $this->service->getAll())
			->withStatus(ResponseHelper::OK);
	}

	/**
	 * @Path("/{id}")
	 * @Method("GET")
	 * @RequestParameters({
	 * 		@RequestParameter(name="id", type="int")
	 *})
	 *
	 * @param ApiRequest $apiRequest
	 * @param ApiResponse $apiResponse
	 * @return ApiResponse
	 */
	public function get(ApiRequest $apiRequest, ApiResponse $apiResponse): ApiResponse
	{
		try {
			$id = $apiRequest->getParameter('id');

			$wishlist = $this->service->get($id);

			return $apiResponse
				->withStatus(ResponseHelper::OK)
				->withAttribute(ResponseEnum::SINGLE, $wishlist);
		} catch (EntityNotFoundException|Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/{id}/products")
	 * @Method("GET")
	 * @RequestParameters({
	 * 		@RequestParameter(name="id", type="int")
	 *})
	 *
	 * @param ApiRequest $apiRequest
	 * @param ApiResponse $apiResponse
	 * @return ApiResponse
	 */
	public function getProducts(ApiRequest $apiRequest, ApiResponse $apiResponse): ApiResponse
	{
		try {
			$id = $apiRequest->getParameter('id');

			$products = $this->service->getProducts($id);

			return $apiResponse
				->withStatus(ResponseHelper::OK)
				->withAttribute(ResponseEnum::MULTIPLE, $products);
		} catch (EntityNotFoundException|Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/")
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

			$wishlist = $this->service->create($valueObject);

			return $response
				->withAttribute(ResponseEnum::SINGLE, $wishlist)
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/{id}")
	 * @Method("PUT")
	 * @RequestParameters({
	 * 		@RequestParameter(name="id", type="int")
	 *})
	 * @Responses({
	 * 		@Response(code="200", description="Success")
	 * })
	 * @RequestBody(entity="App\ValueObject\WishlistValueObject")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function update(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$id = $request->getParameter('id');

			/** @var WishlistValueObject $valueObject */
			$valueObject = $this->getRequestEntity($request);

			$wishlist = $this->service->update($id, $valueObject);

			return $response
				->withAttribute(ResponseEnum::SINGLE, $wishlist)
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/{id}/archive")
	 * @Method("PATCH")
	 * @RequestParameters({
	 * 		@RequestParameter(name="id", type="int")
	 *})
	 * @Responses({
	 * 		@Response(code="204", description="Success")
	 * })
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function archive(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$id = $request->getParameter('id');

			$user = $request->getAttribute(RequestEnum::USER);

			$this->service->archive($id, $user);

			return $response
				->withStatus(ResponseHelper::NO_CONTENT);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}
}
