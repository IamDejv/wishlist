<?php

declare(strict_types=1);

namespace App\ErrorHandler;

use Apitte\Core\Dispatcher\DispatchError;
use Apitte\Core\ErrorHandler\IErrorHandler;
use Apitte\Core\Exception\ApiException;
use Apitte\Core\Exception\Runtime\SnapshotException;
use Apitte\Core\Http\ApiResponse;
use App\Decorator\Traits\CorsResponse;
use GuzzleHttp\Psr7\Response;
use Throwable;

use function GuzzleHttp\Psr7\stream_for;

class ErrorHandler implements IErrorHandler
{
	use CorsResponse;

	private bool $catchException = false;

	public function setCatchException(bool $catchException): void
	{
		$this->catchException = $catchException;
	}

	/**
	 * @param DispatchError $dispatchError
	 * @return ApiResponse
	 * @throws Throwable
	 */
	public function handle(DispatchError $dispatchError): ApiResponse
	{
		$error = $dispatchError->getError();

		// Rethrow error if it should not be catch (debug only)
		if (!$this->catchException) {
			// Unwrap exception from snapshot
			if ($error instanceof SnapshotException) {
				throw $error->getPrevious();
			}

			throw $error;
		}

		// Response is inside snapshot, return it
		if ($error instanceof SnapshotException) {
			return $error->getResponse();
		}

		// No response available, create new from error
		return $this->createResponseFromError($error);
	}

	protected function createResponseFromError(Throwable $error): ApiResponse
	{
		$code = $error->getCode();
		$code = $code < 400 || $code > 600 ? 500 : $code;
		$data = [
			'status' => 'error',
			'code' => $code,
			'message' => $error instanceof ApiException ? $error->getMessage(
			) : 'Application encountered an internal error. Please try again later.',
		];

		if (!$this->catchException) {
			$data["file"] = $error->getFile();
			$data["line"] = $error->getLine();
			$data["previous"] = $error->getPrevious() ? $error->getPrevious() : null;
			$data["trace"] = $error->getTrace();
		}

		if ($error instanceof ApiException && ($context = $error->getContext()) !== null) {
			$data['context'] = $context;
		}

		$body = stream_for(
			json_encode(
				$data,
				JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | (defined(
					'JSON_PRESERVE_ZERO_FRACTION'
				) ? JSON_PRESERVE_ZERO_FRACTION : 0)
			)
		);

		$response = new ApiResponse(new Response());

		return $this->corsResponse($response)
			->withStatus($code)
			->withHeader('Content-Type', 'application/json')
			->withBody($body);
	}
}
