<?php
declare(strict_types=1);

namespace App\Decorator\Response;

use Apitte\Core\Decorator\IResponseDecorator;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Enum\ResponseEnum;
use App\Helpers\ResponseHelper;
use App\Model\Entity\IEntity;

class ResponseMapperDecorator implements IResponseDecorator
{

	public function decorateResponse(ApiRequest $request, ApiResponse $response): ApiResponse
	{
		$response = $response->withHeader('Content-Type', 'application/json');

		$result = [];

		if ($response->getStatusCode() === ResponseHelper::NO_CONTENT) {
			return $response;
		}

		if ($response->hasAttribute(ResponseEnum::MULTIPLE)) {
			$data = $response->getAttribute(ResponseEnum::MULTIPLE);

			/** @var IEntity $item */
			foreach ($data as $item) {
				$result[] = $item->toArray();
			}
		}

		if ($response->hasAttribute(ResponseEnum::SINGLE)) {
			$data = $response->getAttribute(ResponseEnum::SINGLE);
			$result = $data->toArray();
		}

		$response->writeJsonBody($result);

		return $response;
	}
}
