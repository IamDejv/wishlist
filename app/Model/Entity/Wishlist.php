<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Entity\Traits\TId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\WishlistRepository")
 * @ORM\Table(name="`wishlists`")
 * @ORM\HasLifecycleCallbacks
 */
class Wishlist extends BaseEntity
{
	use TId;

	/**
	 * One wishlist has many products. This is the inverse side.
	 * @ORM\OneToMany(targetEntity="Product", mappedBy="wishlist")
	 */
	private Collection $products;

	/**
	 * Many features have one product. This is the owning side.
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="wishlists")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	private User $owner;

	#[Pure]
	public function __construct()
	{
		$this->products = new ArrayCollection();
	}

	/**
	 * @return ArrayCollection|Collection
	 */
	public function getProducts(): ArrayCollection|Collection
	{
		return $this->products;
	}

	/**
	 * @param ArrayCollection|Collection $products
	 */
	public function setProducts(ArrayCollection|Collection $products): void
	{
		$this->products = $products;
	}

	/**
	 * @return User
	 */
	public function getOwner(): User
	{
		return $this->owner;
	}

	/**
	 * @param User $owner
	 */
	public function setOwner(User $owner): void
	{
		$this->owner = $owner;
	}

	#[Pure]
	#[ArrayShape(["id" => "int", "user" => "string"])]
	public function toArray(): array
	{
		return [
			"id" => $this->getId(),
			"user" => $this->getOwner()->getId(),
		];
	}

}
