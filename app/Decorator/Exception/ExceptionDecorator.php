<?php
declare(strict_types=1);

namespace App\Decorator\Exception;


use Apitte\Core\Decorator\IErrorDecorator;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Decorator\Traits\CorsResponse;
use Throwable;

class ExceptionDecorator implements IErrorDecorator
{
	use CorsResponse;

	public function decorateError(ApiRequest $request, ApiResponse $response, Throwable $error): ApiResponse
	{
		$response->writeJsonBody([
			"message" => $error->getMessage(),
			"code" => $error->getCode(),
		]);
		return $this->corsResponse($request, $response);
	}
}
