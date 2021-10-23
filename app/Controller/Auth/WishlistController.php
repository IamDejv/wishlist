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
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Helpers\ResponseHelper;
use App\Service\WishlistService;
use App\ValueObject\Exception\InvalidValueException;
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
		return $response->writeJsonBody($this->service->getAll())
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

			return $apiResponse->writeJsonBody($wishlist->toArray());
		} catch (EntityNotFoundException|Exception $e) {
			return $apiResponse->writeJsonBody([
				'message' => $e->getMessage(),
			])->withStatus(ResponseHelper::BAD_REQUEST);
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

			return $response->writeJsonBody($wishlist->toArray())
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

			return $response->writeJsonBody($wishlist->toArray())
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
