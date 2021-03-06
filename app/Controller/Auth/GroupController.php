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
use App\Enum\RequestEnum;
use App\Enum\ResponseEnum;
use App\Helpers\ResponseHelper;
use App\Service\GroupService;
use App\ValueObject\ActionUserValueObject;
use App\ValueObject\GroupValueObject;
use Doctrine\ORM\EntityNotFoundException;
use Exception;

/**
 * @Path("/groups")
 */
class GroupController extends BaseAuthController
{
	public function __construct(private GroupService $service)
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

			$group = $this->service->get($id);

			return $apiResponse->withAttribute(ResponseEnum::SINGLE, $group);
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
	 * @RequestBody(entity="App\ValueObject\GroupValueObject")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function create(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			/** @var GroupValueObject $valueObject */
			$valueObject = $this->getRequestEntity($request);

			$group = $this->service->create($valueObject);

			return $response
				->withAttribute(ResponseEnum::SINGLE, $group)
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
	 * @RequestBody(entity="App\ValueObject\GroupValueObject")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function update(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$id = $request->getParameter('id');

			/** @var GroupValueObject $valueObject */
			$valueObject = $this->getRequestEntity($request);

			$group = $this->service->update($id, $valueObject);

			return $response
				->withAttribute(ResponseEnum::SINGLE, $group)
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/{id}/users")
	 * @Method("GET")
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
	public function getUsers(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$id = $request->getParameter('id');

			$users = $this->service->getGroupUsers($id);

			return $response
				->withAttribute(ResponseEnum::MULTIPLE, $users)
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}

	/**
	 * @Path("/{id}/users")
	 * @Method("PUT")
	 * @RequestParameters({
	 * 		@RequestParameter(name="id", type="int")
	 *})
	 * @Responses({
	 * 		@Response(code="200", description="Success")
	 * })
	 *
	 * @RequestBody(entity="App\ValueObject\ActionUserValueObject")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function addUser(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			$id = $request->getParameter('id');

			/** @var ActionUserValueObject $valueObject */
			$valueObject = $this->getRequestEntity($request);

			$user = $this->service->actionUser($id, $valueObject);

			return $response
				->withAttribute(ResponseEnum::SINGLE, $user)
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
