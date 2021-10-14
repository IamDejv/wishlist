<?php
declare(strict_types=1);

namespace App\Controller\Auth;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\Responses;
use Apitte\Core\Annotation\Controller\Response;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Helpers\ResponseHelper;
use Kreait\Firebase\Contract\Messaging;

/**
 * @Path("/")
 */
class SubscribeController extends BaseAuthController
{
	public function __construct(private Messaging $messaging)
	{
	}

	/**
	 * @Path("/subscribe")
	 * @Method("POST")
	 * @Responses({
	 *     @Response(code="200", description="Success")
	 * })
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function subscribe(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$messagingToken = $request->getJsonBody()["token"];
		$topic = $request->getJsonBody()["topic"];

		$this->messaging->subscribeToTopic($topic,$messagingToken);
		return $response->writeJsonBody([])->withStatus(ResponseHelper::OK);
	}

	/**
	 * @Path("/unsubscribe")
	 * @Method("DELETE")
	 * @Responses({
	 *     @Response(code="200", description="Success")
	 * })
	 *
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function unsubscribe(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$messagingToken = $request->getJsonBody()["token"];
		$topic = $request->getJsonBody()["topic"];

		$this->messaging->unsubscribeFromTopic($topic,$messagingToken);
		return $response->writeJsonBody([])->withStatus(ResponseHelper::OK);
	}
}
