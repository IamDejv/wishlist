<?php
declare(strict_types=1);

namespace App\Decorator\Traits;

use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;

trait CorsResponse
{
	public function corsResponse(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		return $response->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', 'content-type, accept, Authentication')
			->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, PATCH, OPTIONS, PUT');
	}
}
