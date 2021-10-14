<?php
declare(strict_types=1);

namespace App\ValueObject\Exception;


use Exception;

class InvalidValueException extends Exception
{
	/**
	 * @var array
	 */
	private array $errors = [];

	/**
	 * @param string $message
	 */
	public function setMessage(string $message): void
	{
		$this->message = $message;
	}

	/**
	 * @param array $errors
	 */
	public function setErrors(array $errors): void
	{
		$this->errors = $errors;
	}

	/**
	 * @return array
	 */
	public function getErrors(): array
	{
		return $this->errors;
	}
}
