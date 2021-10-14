<?php
declare(strict_types=1);

namespace App\Decorator\Exception;


use Apitte\Core\Decorator\IErrorDecorator;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Throwable;

class ExceptionDecorator implements IErrorDecorator
{
	public function decorateError(ApiRequest $request, ApiResponse $response, Throwable $error): ApiResponse
	{
		return $response->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', 'content-type, accept, Authentication')
			->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, PATCH, OPTIONS, PUT');
	}
}
