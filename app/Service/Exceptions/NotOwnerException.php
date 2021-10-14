<?php
declare(strict_types=1);

namespace App\Service\Exceptions;


class NotOwnerException extends \Exception
{
	protected $message = "Není váše";
}
