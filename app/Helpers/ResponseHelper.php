<?php
declare(strict_types=1);

namespace App\Helpers;

use Exception;
use JetBrains\PhpStorm\ArrayShape;

class ResponseHelper
{
	public const OK = 200;
	public const CREATED = 201;
	public const UPDATED = 200;
	public const NO_CONTENT = 204;
	public const DELETED = 204;

	public const BAD_REQUEST = 400;
	public const UNAUTHORIZED = 401;
	public const FORBIDDEN = 403;
	public const NOT_FOUND = 404;

	public const INTERNAL_SERVER_ERROR = 500;
	public const UNAVAILABLE = 503;

	public const NOT_REGISTERED = "NOT_REGISTERED";
	public const CREDENTIALS_INCORRECT = "CREDENTIALS_INCORRECT";
	public const TOKEN_EXPIRED = "TOKEN_EXPIRED";
	public const NOT_AUTHENTICATED = "NOT_AUTHENTICATED";

	/**
	 * @param Exception $exception
	 * @return array
	 */
	public static function formatException(Exception $exception): array
	{
		return [
			'message' => $exception->getMessage(),
			'code' => $exception->getCode(),
			'type' => get_class($exception),
		];
	}
}
