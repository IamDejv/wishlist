<?php
declare(strict_types=1);

namespace App\ValueObject\Exception;


use Apitte\Core\Exception\Api\ClientErrorException;
use App\Helpers\ResponseHelper;

class InvalidValueException extends ClientErrorException
{
	protected $code = ResponseHelper::BAD_REQUEST;

	private array $errors = [];

	public function setMessage(string $message): void
	{
		$this->message = $message;
	}

	public function setErrors(array $errors): void
	{
		$this->errors = $errors;
	}

	public function getErrors(): array
	{
		return $this->errors;
	}
}
