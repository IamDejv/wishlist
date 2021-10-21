<?php
declare(strict_types=1);

namespace App\Model\Factory;


use App\Model\Entity\BaseEntity;
use App\Model\Entity\User;
use App\Model\Hydrator\UserHydrator;
use App\ValueObject\ValueObjectInterface;

class UserFactory implements EntityFactoryInterface
{
	public function __construct(private UserHydrator $hydrator)
	{
	}

	public function create(ValueObjectInterface $valueObject): BaseEntity|User|null
	{
		return $this->hydrator->hydrate($valueObject);
	}
}
