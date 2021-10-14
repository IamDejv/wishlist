<?php
declare(strict_types=1);

namespace App\Model\Hydrator;


use App\Model\Entity\BaseEntity;
use App\ValueObject\ValueObjectInterface;

interface HydratorInterface
{
	/**
	 * @param ValueObjectInterface $valueObject
	 * @param BaseEntity|null $entity
	 * @return mixed
	 */
	public function hydrate(ValueObjectInterface $valueObject, ?BaseEntity $entity);
}
