<?php
declare(strict_types=1);

namespace App\Security;


class RoleEnum
{
	const USER = 'User';
	const TRAINER = 'Trainer';
	const MANAGER = 'Manager';
	const ADMINISTRATOR = 'Administrator';

	const USER_ID = 1;
	const TRAINER_ID = 2;
	const MANAGER_ID = 3;
	const ADMINISTRATOR_ID = 4;
}
