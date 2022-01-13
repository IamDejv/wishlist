<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Wishlist;
use Doctrine\ORM\Query\Expr\Join;

class ProductRepository extends BaseRepository
{

	public function findByUserAndActiveWishlist(string $firebaseUid)
	{
		$qb = $this->createQueryBuilder("p");

		$qb
			->leftJoin(Wishlist::class, "w", Join::WITH, "w.id = p.wishlist")
			->where("w.active = 1")
			->andWhere("w.owner = :uid")
			->setParameter("uid", $firebaseUid)
		;
		return $qb->getQuery()->getResult();
	}
}
