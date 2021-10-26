<?php
declare(strict_types=1);

namespace App\Decorator\Exception;


use Apitte\Core\Decorator\IErrorDecorator;
use Apitte\Core\Exception\Api\ClientErrorException;
use Apitte\Core\Exception\Api\ServerErrorException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Decorator\Traits\CorsResponse;
use App\Helpers\ResponseHelper;
use App\ValueObject\Exception\InvalidValueException;
use Nette\Utils\Json;
use Throwable;

class ExceptionDecorator implements IErrorDecorator
{
	use CorsResponse;

	public function decorateError(ApiRequest $request, ApiResponse $response, Throwable $error): ApiResponse
	{
		$response = $response->withHeader('Content-Type', 'application/json');
		if ($error instanceof ClientErrorException) {
			$response = $response
				->withStatus($error->getCode() ?? ResponseHelper::OK);
			$previous = $error->getPrevious();
			if ($previous instanceof InvalidValueException) {
				$response->appendBody(Json::encode([
					"validations" => $previous->getErrors(),
					"message" => $error->getMessage(),
					"code" => $error->getCode(),
				]));
			} else {
				$response->appendBody(Json::encode([
					"message" => $error->getMessage(),
					"code" => $error->getCode(),
				]));
			}
		} else if ($error instanceof ServerErrorException) {
			$response->withStatus($error->getCode() ?? ResponseHelper::INTERNAL_SERVER_ERROR);
			$response->appendBody([
				"message" => $error->getMessage(),
				"code" => $error->getCode(),
			]);
		} else {
			$response->withStatus(ResponseHelper::INTERNAL_SERVER_ERROR);
			$response->appendBody([
				"message" => $error->getMessage(),
				"code" => $error->getCode(),
			]);
		}


		return $this
			->corsResponse($request, $response);
	}
}
