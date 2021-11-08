<?php
declare(strict_types=1);

namespace App\Decorator\Request;

use Apitte\Core\Decorator\IRequestDecorator;
use Apitte\Core\Exception\Runtime\EarlyReturnResponseException;
use Apitte\Core\Http\ApiRequest;
use Apitte\Core\Http\ApiResponse;
use App\Decorator\Traits\CorsResponse;
use App\Enum\RequestEnum;
use App\Helpers\ResponseHelper;
use App\Service\Exceptions\ExpiredTokenException;
use App\Service\TokenService;
use Doctrine\ORM\EntityNotFoundException;
use Exception;
use Firebase\Auth\Token\Exception\InvalidToken;

class AuthenticationDecorator implements IRequestDecorator
{
	use CorsResponse;

	public function __construct(
		private array $endpointPrefixes,
		private array $privateEndpointExceptions,
		private TokenService $tokenService
	)
	{
	}

	/**
	 * @inheritDoc
	 */
	public function decorateRequest(ApiRequest $request, ApiResponse $response): ApiRequest
	{
		if ($request->getMethod() === "OPTIONS") {
			throw new EarlyReturnResponseException($this->corsResponse($response));
		}

//		 If public endpoint is matched in url, its public endpoint OR if is endpoint in private endpoint exceptions
		if ($this->isPrivateEndpointException($request)) {
			return $request->withAttribute("publicEndpoint", true);
		}

		$tokenHeaderValue = $request->getHeader("Authentication");
		if (empty($tokenHeaderValue)) {
			$this->notAuthenticated($response, "Header Authentication not included");
		}

		try {
			$user = $this->tokenService->checkToken($tokenHeaderValue[0], true);
			$request = $request->withAttribute(RequestEnum::USER, $user);
		} catch (ExpiredTokenException) {
			$this->notAuthenticated($response, "Authentication error!", ResponseHelper::TOKEN_EXPIRED);
		} catch (EntityNotFoundException) {
			$this->notAuthenticated($response, "Authentication error!", ResponseHelper::NOT_REGISTERED);
		} catch (InvalidToken) {
			$this->notAuthenticated($response, "Authentication error!", ResponseHelper::INVALID_TOKEN);
		} catch (Exception) {
			$this->notAuthenticated($response, "Authentication error!");
		}

		return $request;
	}

	private function notAuthenticated(ApiResponse $response, string $message, string $code = ResponseHelper::NOT_AUTHENTICATED)
	{
		$response = $this->corsResponse($response)
			->writeJsonBody(["message" => $message, "code" => $code])
			->withStatus(ResponseHelper::UNAUTHORIZED);
		throw new EarlyReturnResponseException($response);
	}

	private function isPrivateEndpointException(ApiRequest $apiRequest): bool
	{
		$privateExceptions = $this->privateEndpointExceptions;

		foreach ($privateExceptions as $privateException) {
			if ($privateException['uri'] === $this->normalizeUriFromSpecificIds($apiRequest) &&
				$privateException['type'] === $apiRequest->getMethod()) {
				return true;
			}
		}

		return false;
	}

	private function normalizeUriFromSpecificIds(ApiRequest $request): string
	{
		return preg_replace('/[0-9]+/', '*', $request->getUri()->getPath());
	}
}
