<?php
declare(strict_types=1);

namespace App\Model;

use Doctrine\Persistence\ObjectRepository;
use App\Model\Repository\BaseRepository;
use Nettrine\ORM\EntityManagerDecorator;

class EntityManager extends EntityManagerDecorator
{
	/**
	 * @param string $entityName
	 * @return ObjectRepository|BaseRepository
	 * @internal
	 */
	public function getRepository($entityName): ObjectRepository
	{
		return parent::getRepository($entityName);
	}
}
