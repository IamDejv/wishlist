<?php
declare(strict_types=1);

namespace App\Decorator\Request;

use Apitte\Core\Decorator\IRequestDecorator;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use Apitte\Core\Http\RequestAttributes;
use Apitte\Core\Schema\Endpoint;
use App\Enum\RequestEnum;
use Contributte\Psr7\Exception\Logical\InvalidStateException;
use Nette\Utils\Paginator;

class PaginationDecorator implements IRequestDecorator
{

	/**
	 * @inheritDoc
	 */
	public function decorateRequest(ApiRequest $request, ApiResponse $response): ApiRequest
	{
		/** @var Endpoint $endpoint */
		$endpoint = $request->getAttribute(RequestAttributes::ATTR_ENDPOINT);

		if($endpoint->hasTag(RequestEnum::PAGINATION)){

			try{
				$page = $request->getQueryParam("page", 1);
				$limit = $request->getQueryParam("limit", 10);
			}catch (InvalidStateException){
			}


			$paginator = new Paginator();
			$paginator->setPage($page ? intval($page) : 1);
			$paginator->setItemsPerPage($limit ? intval($limit) : 10);

			$request = $request->withAttribute(RequestEnum::PAGINATION, $paginator);
		}

		return $request;
	}
}
