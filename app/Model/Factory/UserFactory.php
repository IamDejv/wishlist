<?php
declare(strict_types=1);

namespace App\Model\Factory;


use App\Model\Entity\BaseEntity;
use App\Model\Hydrator\UserHydrator;
use App\ValueObject\ValueObjectInterface;

class UserFactory implements EntityFactoryInterface
{
	private UserHydrator $hydrator;

	/**
	 * UserFactory constructor.
	 * @param UserHydrator $hydrator
	 */
	public function __construct(UserHydrator $hydrator)
	{
		$this->hydrator = $hydrator;
	}

	/**
	 * @param ValueObjectInterface $valueObject
	 * @return BaseEntity|null
	 */
	public function create(ValueObjectInterface $valueObject): ?BaseEntity
	{
		return $this->hydrator->hydrate($valueObject);
	}
}
