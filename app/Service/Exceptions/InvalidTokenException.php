<?php
declare(strict_types=1);

namespace App\Service\Exception;

class InvalidTokenException extends TokenException
{
	/**
	 * @var string
	 */
	protected $message = 'Token is invalid.';

	/**
	 * @var int
	 */
	protected $code = 401;
}
