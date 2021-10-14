<?php
declare(strict_types=1);

namespace App\Decorator\Response;


use Apitte\Core\Decorator\IResponseDecorator;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

class CorsDecorator implements IResponseDecorator
{
	/**
	 * @param ApiRequest $request
	 * @param ApiResponse $response
	 * @return ApiResponse
	 */
	public function decorateResponse(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', 'content-type, accept, Authentication')
			->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, PATCH, OPTIONS, PUT');
	}
}
