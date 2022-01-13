<?php

declare(strict_types=1);

namespace App\Filter;

use App\Model\Entity\Group;
use App\Model\Entity\Wishlist;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use JetBrains\PhpStorm\Pure;

class ArchivedFilter extends SQLFilter
{
	#[Pure]
	public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
	{
		if ($targetEntity->getReflectionClass()->getName() === Wishlist::class || $targetEntity->getReflectionClass()->getName() === Group::class) {
			return $targetTableAlias . '.archived = false';
		}
		return '';
	}
}
