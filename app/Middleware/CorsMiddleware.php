<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Decorator\Traits\CorsResponse;
use Contributte\Middlewares\IMiddleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CorsMiddleware implements IMiddleware
{
	use CorsResponse;

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @param callable $next
	 * @return ResponseInterface
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
	{
		$response = $this->corsResponse($response);
		if ($request->getMethod() === 'OPTIONS') {
			return $response;
		}

		return $next($request, $response);
	}

}
