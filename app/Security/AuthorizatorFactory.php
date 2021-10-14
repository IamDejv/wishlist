<?php
declare(strict_types=1);

namespace App\Security;


class AuthorizatorFactory
{
	/**
	 * @return Permission
	 */
	public static function create(): Permission
	{
		return new Permission();
	}
}
