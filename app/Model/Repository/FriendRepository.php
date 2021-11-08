<?php
declare(strict_types=1);

namespace App\Model\Repository;

use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query\Expr\Andx;

class FriendRepository extends BaseRepository
{

	/**
	 * @throws NonUniqueResultException
	 */
	public function findByUsers(string $id, string $friendId)
	{
		$qb = $this->createQueryBuilder("f");
		$qb
			->where($qb->expr()->andX(new Andx([
					$qb->expr()->eq("f.friend", ":friendId"),
					$qb->expr()->eq("f.user", ":userId"),
				])
			))
			->orWhere($qb->expr()->andX(new Andx([
					$qb->expr()->eq("f.user", ":friendId"),
					$qb->expr()->eq("f.friend", ":userId"),
				])
			))
			->setParameter("userId", $id)
			->setParameter("friendId", $friendId)
		;

		return $qb->getQuery()->getOneOrNullResult();
	}
}
