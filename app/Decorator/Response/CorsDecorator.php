<?php
declare(strict_types=1);

namespace App\Decorator\Response;


use Apitte\Core\Decorator\IResponseDecorator;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Decorator\Traits\CorsResponse;

class CorsDecorator implements IResponseDecorator
{
	use CorsResponse;

	/**
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function decorateResponse(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $this->corsResponse($response);
	}
}
