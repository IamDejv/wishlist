<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\RequestBody;
use Apitte\Core\Annotation\Controller\RequestParameter;
use Apitte\Core\Annotation\Controller\RequestParameters;
use Apitte\Core\Annotation\Controller\Response;
use Apitte\Core\Annotation\Controller\Responses;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Enum\RequestEnum;
use App\Enum\ResponseEnum;
use App\Helpers\ResponseHelper;
use App\Model\Entity\User;
use App\Service\ProductService;
use App\ValueObject\ProductValueObject;
use App\ValueObject\UpdateProductValueObject;
use Doctrine\ORM\EntityNotFoundException;
use Exception;

/**
 * @Path("/products")
 */
class ProductController extends BaseAuthController
{
	public function __construct(private ProductService $service)
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

			$product = $this->service->get($id);

			return $apiResponse
				->withAttribute(ResponseEnum::SINGLE, $product);
		} catch (EntityNotFoundException | Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/{id}")
	 * @Method("DELETE")
	 * @RequestParameters({
	 * 		@RequestParameter(name="id", type="int")
	 *})
	 *
	 * @param ApiRequest $apiRequest
	 * @param ApiResponse $apiResponse
	 * @return ApiResponse
	 */
	public function delete(ApiRequest $apiRequest, ApiResponse $apiResponse): ApiResponse
	{
		try {
			$id = $apiRequest->getParameter('id');

			$this->service->delete($id);

			return $apiResponse
				->withStatus(ResponseHelper::NO_CONTENT);
		} catch (EntityNotFoundException | Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/")
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
	public function create(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			/** @var ProductValueObject $valueObject */
			$valueObject = $this->getRequestEntity($request);

			$product = $this->service->create($valueObject);

			return $response
				->withAttribute(ResponseEnum::SINGLE, $product)
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
	 * @RequestBody(entity="App\ValueObject\UpdateProductValueObject")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function update(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$id = $request->getParameter('id');

			/** @var UpdateProductValueObject $valueObject */
			$valueObject = $this->getRequestEntity($request);

			$product = $this->service->update($id, $valueObject);

			return $response
				->withAttribute(ResponseEnum::SINGLE, $product)
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/{id}/reserve")
	 * @Method("PATCH")
	 * @RequestParameters({
	 * 		@RequestParameter(name="id", type="int")
	 *})
	 * @Responses({
	 * 		@Response(code="200", description="Success")
	 * })
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function reserve(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$id = $request->getParameter('id');

		/** @var User $user */
		$user = $request->getAttribute(RequestEnum::USER);

		$product = $this->service->reserve($id, $user);

		return $response
			->withAttribute(ResponseEnum::SINGLE, $product)
			->withStatus(ResponseHelper::OK);
	}
}
