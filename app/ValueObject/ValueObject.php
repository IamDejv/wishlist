<?php
declare(strict_types=1);

namespace App\ValueObject;


use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validation;
use App\Mapping\BaseRequestEntity;
use App\ValueObject\Exception\InvalidValueException;

abstract class ValueObject extends BaseRequestEntity implements ValueObjectInterface
{
	/**
	 * @return ValueObject|null
	 * @throws InvalidValueException
	 */
	public function validate(): ?ValueObjectInterface
	{
		$validator = Validation::createValidatorBuilder()
			->enableAnnotationMapping()
			->getValidator();

		$result = $validator->validate($this);

		if ($result->count() > 0) {
			$errors = [];

			for ($i = 0; $i < $result->count(); $i++) {
				/** @var ConstraintViolation $error */
				$error = $result->get($i);

				if (!isset($errors[$error->getPropertyPath()])) {
					$errors[$error->getPropertyPath()] = [
						'field' => $error->getPropertyPath(),
						'validationErrors' => []
					];
				}

				$errors[$error->getPropertyPath()]['validationErrors'][] = [
					'errorMessage' => $error->getMessage(),
					'providedValue' => $error->getInvalidValue(),
					'errorCode' => $error->getCode()
				];

			}

			$exception = new InvalidValueException();
			$exception->setMessage('Validation failed');
			$exception->setErrors($errors);

			throw $exception;
		}

		return $this;
	}

	/**
	 * @param array $array
	 * @return $this
	 * @throws InvalidValueException
	 */
	public function fillPropertiesFromArray(array $array)
	{
		foreach ($array as $index => $value) {
			$this->{$index} = $value;
		}

		$this->validate();

		return $this;
	}
}
