<?php
declare(strict_types=1);

namespace App\Model\Repository;

use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;

class UserRepository extends BaseRepository
{
	public function findByCriteria(Criteria $criteria, array $orderBy, int $perPage, int $offset)
	{
		$qb = $this->createQueryBuilder("u");

		$qb->addCriteria($criteria);

		foreach ($orderBy as $key => $value) {
			$qb->addOrderBy($key, $value);
		}

		$qb->setFirstResult($offset);
		$qb->setMaxResults($perPage);

		return $qb->getQuery()->getResult();
	}

	public function findUsers(string $id, ?string $searchValue, array $myFriends, array $orderBy, int $perPage, int $offset)
	{
		$qb = $this->createQueryBuilder("u");
		$qb = $qb->where($qb->expr()->neq("u.id", ":id"))
			->setParameter("id", $id)
		;

		if (!is_null($searchValue)) {
			$qb->andWhere($qb->expr()->orX(
				$qb->expr()->like("u.firstname", ":search"),
				$qb->expr()->like("u.lastname", ":search")
			))
				->setParameter("search", "%$searchValue%");
		}

		foreach ($myFriends as $key => $friend) {
			$qb->andWhere($qb->expr()->neq("u.id", ":friend$key"))->setParameter("friend$key", $friend["friend"]);
		}


		foreach ($orderBy as $key => $value) {
			$qb = $qb->addOrderBy($key, $value);
		}

		$qb = $qb->setFirstResult($offset);
		$qb->setMaxResults($perPage);

		return $qb->getQuery()->getResult();
	}

	public function findFriends(string $id)
	{
		$qb = $this->createQueryBuilder("u");
	}
}
