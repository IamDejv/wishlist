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
use App\Util\LinkAnalyzer;
use App\ValueObject\AnalyzeLinkValueObject;
use Exception;

/**
 * @Path("/service")
 */
class ServiceController extends BaseAuthController
{
	public function __construct(
		private LinkAnalyzer $linkAnalyzer,
	)
	{
	}

	/**
	 * @Path("/link")
	 * @Method("POST")
	 * @Responses({
	 * 		@Response(code="200", description="Success")
	 * })
	 * @RequestBody(entity="App\ValueObject\AnalyzeLinkValueObject")
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function update(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		try {
			/** @var AnalyzeLinkValueObject $valueObject */
			$valueObject = $this->getRequestEntity($request);

			$productInfo = $this->linkAnalyzer->analyze($valueObject->getLink());


			return $response
				->withAttribute(ResponseEnum::SIMPLE, $productInfo)
				->withStatus(ResponseHelper::OK);
		} catch (Exception $e) {
			throw new ClientErrorException($e->getMessage(), ResponseHelper::BAD_REQUEST, $e);
		}
	}
}
