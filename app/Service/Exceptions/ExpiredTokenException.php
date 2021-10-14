<?php
declare(strict_types=1);

namespace App\Service\Exception;

class ExpiredTokenException extends TokenException
{
	/**
	 * @var string
	 */
	protected $message = 'Token is expired.';

	/**
	 * @var int
	 */
	protected $code = 401;
}
