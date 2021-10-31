<?php
declare(strict_types=1);

namespace App\Model\Repository;

use App\Model\Entity\Product;
use Doctrine\ORM\Query\ResultSetMapping;

class ProductRepository extends BaseRepository
{

	public function findByUserAndActiveWishlist(string $firebaseUid)
	{
		$rsm = new ResultSetMapping();
		$rsm->addEntityResult(Product::class, "p");
		$rsm->addFieldResult("p", "id", "id");
		$rsm->addFieldResult("p", "description", "description");
		$rsm->addFieldResult("p", "name", "name");
		$rsm->addFieldResult("p", "image", "image");
		$rsm->addFieldResult("p", "url", "url");
		$rsm->addFieldResult("p", "price", "price");
		$rsm->addFieldResult("p", "reserved", "reserved");

		$qb = $this->_em->createNativeQuery("
			SELECT *
			FROM products AS p
			WHERE p.wishlist_id = (
				SELECT w.id
				FROM wishlists AS w
				WHERE w.owner_id = '$firebaseUid'
				AND w.active = 1
			)
			ORDER BY p.reserved
		", $rsm);

		return $qb->getResult();
	}
}
