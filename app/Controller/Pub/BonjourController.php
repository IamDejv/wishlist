<?php
declare(strict_types=1);

namespace App\Controller\Pub;

use Apitte\Core\Annotation\Controller\Method;
use Apitte\Core\Annotation\Controller\Path;
use Apitte\Core\Annotation\Controller\Responses;
use Apitte\Core\Annotation\Controller\Response;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Nette\Utils\DateTime;

/**
 * @Path("/bonjour")
 */
class BonjourController extends BasePubController
{
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
	public function bonjour(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response->writeJsonBody([
			"message" => "I'm here and listening",
			"date" => (new DateTime())->format("Y-m-d H:i:s")
		]);
	}
}
