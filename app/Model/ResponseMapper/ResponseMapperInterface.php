<?php
declare(strict_types=1);

namespace App\Model\ResponseMapper;


use App\Model\Entity\BaseEntity;

interface ResponseMapperInterface
{
	/**
	 * @param BaseEntity $entity
	 * @return array
	 */
	public function toArray(BaseEntity $entity): array;
}
