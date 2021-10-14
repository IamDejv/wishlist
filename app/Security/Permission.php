<?php
declare(strict_types=1);

namespace App\Security;


use Nette\Security\Permission as NettePermission;

class Permission extends NettePermission
{
	public function __construct()
	{
		$this->addRoles();
		$this->addResources();
		$this->setup();
	}

	/**
	 * @param string|null $role
	 * @param string|null $resource
	 * @param string|null $privilege
	 * @return bool
	 */
	public function isAllowed($role = NettePermission::ALL, $resource = NettePermission::ALL, $privilege = NettePermission::ALL): bool
	{
		return NettePermission::isAllowed($role, $resource, $privilege);
	}

	private function addRoles()
	{
		$this->addRole(RoleEnum::USER);
		{
			$this->addRole(RoleEnum::TRAINER, RoleEnum::USER);
			{
				$this->addRole(RoleEnum::MANAGER, RoleEnum::TRAINER);
				{
					$this->addRole(RoleEnum::ADMINISTRATOR, RoleEnum::MANAGER);
				}
			}
		}
	}

	private function addResources()
	{
		foreach (ResourceEnum::$all as $resource) {
			$this->addResource($resource);
		}
	}

	private function setup()
	{
		$this->setupUser();
		$this->setupTrainer();
		$this->setupManager();
		$this->setupAdministrator();
	}

	private function setupUser()
	{
		$role = RoleEnum::USER;
		$this->allow($role, ResourceEnum::USER, [
			PrivilegeEnum::API_GET,
			PrivilegeEnum::API_LIST,
		]);
		$this->allow($role, ResourceEnum::ME, [
			PrivilegeEnum::API_GET,
			PrivilegeEnum::API_UPDATE,
		]);
		$this->allow($role, ResourceEnum::ATTENDEE, [
			PrivilegeEnum::API_GET,
			PrivilegeEnum::LIST_MY,
			PrivilegeEnum::API_CREATE,
			PrivilegeEnum::API_UPDATE,
			PrivilegeEnum::ACTIVATE,
			PrivilegeEnum::DISABLE,
		]);
		$this->allow($role, ResourceEnum::ATTENDANCE, [
			PrivilegeEnum::GET_BY_ATTENDEE,
			PrivilegeEnum::EXCUSE,
		]);
		$this->allow($role, ResourceEnum::EVENT, [
			PrivilegeEnum::PUBLISHED_TRAININGS,
			PrivilegeEnum::PUBLISHED_ACTIONS,
			PrivilegeEnum::LIST_PUBLISHED,
			PrivilegeEnum::ASSIGN,
		]);
		$this->allow($role, ResourceEnum::TERM, [
			PrivilegeEnum::API_LIST,
			PrivilegeEnum::API_CREATE,
			PrivilegeEnum::API_UPDATE,
			PrivilegeEnum::API_DELETE,
		]);
	}

	private function setupTrainer()
	{
		$role = RoleEnum::TRAINER;
		$this->allow($role, ResourceEnum::USER, [
			PrivilegeEnum::API_GET,
			PrivilegeEnum::API_LIST,
			PrivilegeEnum::LIST_TRAINERS,
			PrivilegeEnum::LIST_USERS,
		]);
		$this->allow($role, ResourceEnum::ATTENDEE, [
			PrivilegeEnum::API_LIST,
			PrivilegeEnum::UPDATE_CARD,
		]);
		$this->allow($role, ResourceEnum::EVENT, [
			PrivilegeEnum::API_LIST,
			PrivilegeEnum::LIST_ACTIONS,
			PrivilegeEnum::LIST_TRAININGS,
			PrivilegeEnum::API_LIST,
			PrivilegeEnum::GET_ATTENDANCE,
		]);
		$this->allow($role, ResourceEnum::ATTENDANCE, [
			PrivilegeEnum::CHECK,
		]);
		$this->allow($role, ResourceEnum::SUBSCRIBE, [
			PrivilegeEnum::SUBSCRIBE,
			PrivilegeEnum::UNSUBSCRIBE,
		]);
	}

	private function setupManager()
	{
		$role = RoleEnum::MANAGER;
		$this->allow($role, ResourceEnum::TERM, [
			PrivilegeEnum::CREATE_NOT_MY,
			PrivilegeEnum::DELETE_NOT_MY,
			PrivilegeEnum::EDIT_NOT_MY,
		]);
		$this->allow($role, ResourceEnum::EVENT, [
			PrivilegeEnum::API_CREATE,
			PrivilegeEnum::API_UPDATE,
			PrivilegeEnum::HIDE,
			PrivilegeEnum::PUBLISH,
		]);
	}

	private function setupAdministrator()
	{
		foreach (ResourceEnum::$all as $resource) {
			$this->allow(RoleEnum::ADMINISTRATOR, $resource, NettePermission::ALL);
		}
	}
}
