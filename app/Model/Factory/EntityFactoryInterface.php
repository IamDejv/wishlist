<?php
declare(strict_types=1);

namespace App\Model\Factory;

use App\Model\Entity\BaseEntity;
use App\ValueObject\ValueObjectInterface;

interface EntityFactoryInterface
{
	public function create(ValueObjectInterface $valueObject): ?BaseEntity;
}
