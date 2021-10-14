<?php
declare(strict_types=1);

namespace App\Service;

use App\Model\Repository\BaseRepository;
use Nettrine\ORM\EntityManagerDecorator;

abstract class BaseService
{
	protected int $config = -1;

	protected EntityManagerDecorator $em;

	public function __construct(private BaseRepository $repository)
	{
		$this->setRepository($repository);
	}

	/**
	 * @return array
	 */
	public function findAll(): array
	{
		return $this->repository->findAll();
	}

	/**
	 * @return BaseRepository
	 */
	public function getRepository(): BaseRepository
	{
		return $this->repository;
	}

	/**
	 *
	 * @param BaseRepository $repository
	 * @return BaseService
	 */
	public function setRepository(BaseRepository $repository): BaseService
	{
		$this->repository = $repository;
		return $this;
	}
}
