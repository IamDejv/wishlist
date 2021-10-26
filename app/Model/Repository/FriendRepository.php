<?php

namespace App\Model\Repository;

class FriendRepository extends BaseRepository
{

	public function findFriends(string $id)
	{
		$qb = $this->createQueryBuilder("f");
		$qb
			->select(["f.friend"])
			->where($qb->expr()->eq("f.user", ":id"))
			->setParameter("id", $id);

		return $qb->getQuery()->getResult();
	}
}
