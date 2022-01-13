<?php

declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\User;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\Expr\Join;
use Doctrine\ORM\Query\ResultSetMapping;

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

	public function findUsers(
		string $id,
		?string $searchValue,
		array $myFriends,
		array $orderBy,
		int $perPage,
		int $offset
	) {
		$qb = $this->createQueryBuilder("u");
		$qb = $qb
			->where($qb->expr()->neq("u.id", ":id"))
			->setParameter("id", $id);

		if (!is_null($searchValue)) {
			$qb->andWhere(
				$qb->expr()->orX(
					$qb->expr()->like("u.firstname", ":search"),
					$qb->expr()->like("u.lastname", ":search"),
					$qb->expr()->like("u.email", ":search")
				)
			)
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
		$rsm = new ResultSetMapping();
		$rsm->addScalarResult('friend', 'friend');

		$qb = $this
			->_em
			->createNativeQuery(
				"
				SELECT IF(f.friend_id = :id, f.user_id, f.friend_id) as friend
				FROM friends as f
				WHERE f.user_id = :id
				OR f.friend_id = :id;
			",
				$rsm
			)->setParameter(":id", $id);

		return $qb->getResult();
	}

	/**
	 * @param int $id
	 * @return User[]
	 */
	public function getGroupUsers(int $id): array
	{
		$rsm = new ResultSetMapping();
		$rsm->addEntityResult(User::class, "u");
		$rsm->addFieldResult('u', 'id', 'id');
		$rsm->addFieldResult('u', 'firstname', 'firstname');
		$rsm->addFieldResult('u', 'lastname', 'lastname');
		$rsm->addFieldResult('u', 'email', 'email');

		$qb = $this->_em->createNativeQuery(
			"
			SELECT u.firstname, u.lastname, u.id, u.email
			FROM users_groups as ug
			LEFT JOIN users AS u ON ug.user_id = u.id
			WHERE ug.group_id = :id
			",
			$rsm
		)->setParameter("id", $id);

		return $qb->getResult();
	}

	public function getUserFriends(string $id)
	{
		$rsm = new ResultSetMapping();
		$rsm->addEntityResult(User::class, "u");
		$rsm->addFieldResult('u', 'id', 'id');
		$rsm->addFieldResult('u', 'firstname', 'firstname');
		$rsm->addFieldResult('u', 'lastname', 'lastname');
		$rsm->addFieldResult('u', 'email', 'email');

		$qb = $this->_em->createNativeQuery(
			"
			SELECT *
			FROM users as u
			WHERE u.id IN (
				SELECT IF(f.friend_id = :id, f.user_id, f.friend_id)
				FROM friends as f
				WHERE f.confirmed = true
				AND (f.user_id = :id
				OR f.friend_id = :id)
			)
			",
			$rsm
		)->setParameter("id", $id);

		return $qb->getResult();
	}

	public function findPendingFriends(string $id)
	{
		$rsm = new ResultSetMapping();
		$rsm->addEntityResult(User::class, "u");
		$rsm->addFieldResult('u', 'id', 'id');
		$rsm->addFieldResult('u', 'firstname', 'firstname');
		$rsm->addFieldResult('u', 'lastname', 'lastname');
		$rsm->addFieldResult('u', 'email', 'email');

		$qb = $this->_em->createNativeQuery(
			"
			SELECT *
			FROM users as u
			WHERE u.id IN (
				SELECT f.user_id
				FROM friends as f
				WHERE f.confirmed = false
				AND f.friend_id = :id
			)
			",
			$rsm
		)->setParameter("id", $id);

		return $qb->getResult();
	}
}
