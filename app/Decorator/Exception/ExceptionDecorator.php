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
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\Json;
use Throwable;

class ExceptionDecorator implements IErrorDecorator
{
	use CorsResponse;

	public function decorateError(ApiRequest $request, ApiResponse $response, Throwable $error): ApiResponse
	{
		$response = $response->withHeader('Content-Type', 'application/json');
		$response = $response->withStatus(ResponseHelper::INTERNAL_SERVER_ERROR);

		if ($error instanceof ClientErrorException) {
			$response = $this->fillResponseByClientException($error, $response);
		} else if ($error instanceof ServerErrorException) {
			$response = $this->fillResponseByServerException($error, $response);
		} else {
			$response = $response
				->withStatus(ResponseHelper::INTERNAL_SERVER_ERROR)
				->appendBody([
					"message" => $error->getMessage(),
					"code" => $error->getCode(),
					"file" => $error->getFile(),
					"line" => $error->getLine(),
				]);
		}

		return $this->corsResponse($request, $response);
	}

	private function fillResponseByClientException(ClientErrorException $error, ApiResponse $response): ApiResponse
	{
		$previous = $error->getPrevious();
		if (!is_null($previous)) {
			if ($previous instanceof InvalidValueException) {
				$response->withStatus(ResponseHelper::BAD_REQUEST);
				$response->appendBody(Json::encode([
					"validations" => $previous->getErrors(),
					"message" => $previous->getMessage(),
					"code" => $previous->getCode(),
				]));
			} else if ($previous instanceof EntityNotFoundException) {
				$response->withStatus(ResponseHelper::NOT_FOUND);
				$response->appendBody(Json::encode([
					"message" => $previous->getMessage(),
					"code" => $previous->getCode(),
				]));
			} else {
				$response->withStatus(ResponseHelper::BAD_REQUEST);
				$response->appendBody(Json::encode([
					"message" => $previous->getMessage(),
					"code" => $previous->getCode(),
				]));
			}
		} else {
			$response->withStatus(ResponseHelper::BAD_REQUEST);
			$response->appendBody(Json::encode([
				"message" => $error->getMessage(),
				"code" => $error->getCode(),
			]));
		}

		return $response;
	}

	private function fillResponseByServerException(ServerErrorException $error, ApiResponse $response): ApiResponse
	{
		$previous = $error->getPrevious();
		if (!is_null($previous)) {
			$response->withStatus(ResponseHelper::INTERNAL_SERVER_ERROR);
			$response->appendBody(Json::encode([
				"message" => $previous->getMessage(),
				"code" => $previous->getCode(),
			]));
		}

		return $response;
	}
}
