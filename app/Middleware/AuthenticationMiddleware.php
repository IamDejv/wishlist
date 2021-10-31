<?php
declare(strict_types=1);

namespace App\Middleware;

use App\Helpers\ResponseHelper;
use App\Service\Exceptions\ExpiredTokenException;
use App\Service\TokenService;
use Contributte\Middlewares\IMiddleware;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Firebase\Auth\Token\Exception\InvalidToken;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class AuthenticationMiddleware implements IMiddleware
{
	public function __construct(private array $endpointPrefixes, private array $privateEndpointExceptions, private TokenService $tokenService)
	{
	}

	/**
	 * @param ServerRequestInterface $request
	 * @param ResponseInterface $response
	 * @param callable $next
	 * @return ResponseInterface
	 */
	public function __invoke(ServerRequestInterface $request, ResponseInterface $response, callable $next): ResponseInterface
	{
		if ($request->getMethod() === 'OPTIONS') {
			return $this->responseWithCorrectHeaders($response);
		}

//		 If public endpoint is matched in url, its public endpoint OR if is endpoint in private endpoint exceptions
		if ($this->isPrivateEndpointException($request)) {
			return $next($request->withAttribute('publicEndpoint', true), $response);
		}

		$tokenHeaderValue = $request->getHeader('Authentication');
		if (empty($tokenHeaderValue)) {
			return $this->notAuthenticated($response, 'Header Authentication not included');
		}

		try {
			if (false) {
				$this->tokenService->checkToken($tokenHeaderValue[0]);
			}
		} catch (ExpiredTokenException $e) {
			return $this->notAuthenticated($response, 'Authentication error!', ResponseHelper::TOKEN_EXPIRED);
		} catch (EntityNotFoundException $e) {
			return $this->notAuthenticated($response, 'Authentication error!', ResponseHelper::NOT_REGISTERED);
		} catch (InvalidToken $e) {
			return $this->notAuthenticated($response, 'Authentication error!', ResponseHelper::INVALID_TOKEN);
		} catch (Exception $e) {
			return $this->notAuthenticated($response, 'Authentication error!');
		}

		return $next($request, $response);
	}

	/**
	 * @param string $uri
	 * @return string
	 */
	private function normalizeUri(
		string $uri
	): string {
		if (substr($uri, -1) === '/') {
			return substr($uri, 0, -1);
		}

		return $uri;
	}

	/**
	 * @param ServerRequestInterface $request
	 * @return string
	 */
	private function getUriPath(ServerRequestInterface $request): string
	{
		return $this->normalizeUri($request->getUri()->getPath());
	}

	/**
	 * @return string
	 */
	private function getPublicEndpoints(): string
	{
		return $this->endpointPrefixes['public'];
	}

	/**
	 * @return array
	 */
	private function getPrivateEndpointExceptions(): array
	{
		return $this->privateEndpointExceptions;
	}

	/**
	 * @param ServerRequestInterface $apiRequest
	 * @return bool
	 */
	private function isPrivateEndpointException(ServerRequestInterface $apiRequest): bool
	{
		$privateExceptions = $this->getPrivateEndpointExceptions();

		foreach ($privateExceptions as $privateException) {
			if ($privateException['uri'] === $this->normalizeUriFromSpecificIds($apiRequest) &&
				$privateException['type'] === $apiRequest->getMethod()) {
				return true;
			}
		}

		return false;
	}

	private function normalizeUriFromSpecificIds(ServerRequestInterface $request): string
	{
		return preg_replace('/[0-9]+/', '*', $request->getUri()->getPath());
	}

	/**
	 * @param ResponseInterface $response
	 * @return ResponseInterface
	 */
	private function responseWithCorrectHeaders(ResponseInterface $response): ResponseInterface
	{
		return $response
			->withHeader('Access-Control-Allow-Origin', '*')
			->withHeader('Access-Control-Allow-Headers', 'content-type, accept, Authentication')
			->withHeader('Access-Control-Allow-Methods', 'GET, POST, DELETE, PATCH, OPTIONS, PUT');
	}

	/**
	 * @param ResponseInterface $response
	 * @param string $message
	 * @param string|null $code
	 * @return ResponseInterface
	 */
	private function notAuthenticated(ResponseInterface $response, string $message, string $code = null): ResponseInterface
	{
		$response->getBody()->write(json_encode([
			'message' => $message,
			'code' => $code,
		]));

		return $this->responseWithCorrectHeaders($response)
			->withHeader('Content-Type', 'application/json')
			->withStatus(ResponseHelper::UNAUTHORIZED);
	}
}
