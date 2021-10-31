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
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
	 */
	private User $owner;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 */
	private string $image;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 */
	private string $name;

	/**
	 * @ORM\Column(type="boolean", nullable=false)
	 */
	private bool $archived;

	/**
	 * @ORM\Column(type="boolean", nullable=false)
	 */
	private bool $active;

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

	/**
	 * @return bool
	 */
	public function isArchived(): bool
	{
		return $this->archived;
	}

	/**
	 * @param bool $archived
	 */
	public function setArchived(bool $archived): void
	{
		$this->archived = $archived;
	}

	/**
	 * @return bool
	 */
	public function isActive(): bool
	{
		return $this->active;
	}

	/**
	 * @param bool $active
	 */
	public function setActive(bool $active): void
	{
		$this->active = $active;
	}

	/**
	 * @return string
	 */
	public function getImage(): string
	{
		return $this->image;
	}

	/**
	 * @param string $image
	 */
	public function setImage(string $image): void
	{
		$this->image = $image;
	}

	/**
	 * @return string
	 */
	public function getName(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName(string $name): void
	{
		$this->name = $name;
	}

	#[ArrayShape(["id" => "int", "image" => "string", "name" => "string", "active" => "bool"])]
	public function toArray(): array
	{
		return [
			"id" => $this->id,
			"image" => $this->image,
			"name" => $this->name,
			"active" => $this->active,
		];
	}

}
