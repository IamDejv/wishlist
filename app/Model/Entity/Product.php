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
 * @ORM\Entity(repositoryClass="App\Model\Repository\ProductRepository")
 * @ORM\Table(name="`products`")
 * @ORM\HasLifecycleCallbacks
 */
class Product extends BaseEntity
{
	use TId;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 */
	private string $name;

	/**
	 * @ORM\Column(type="text", nullable=false)
	 */
	private string $description;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private ?string $image;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private ?string $url;

	/**
	 * @ORM\Column(type="float", nullable=true)
	 */
	private ?float $price;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private bool $reserved;

	/**
	 * Many features have one product. This is the owning side.
	 * @ORM\ManyToOne(targetEntity="Wishlist", inversedBy="products")
	 * @ORM\JoinColumn(name="wishlist_id", referencedColumnName="id")
	 */
	private Wishlist $wishlist;

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

	/**
	 * @return string|null
	 */
	public function getImage(): ?string
	{
		return $this->image;
	}

	/**
	 * @param string|null $image
	 */
	public function setImage(?string $image): void
	{
		$this->image = $image;
	}

	/**
	 * @return string|null
	 */
	public function getUrl(): ?string
	{
		return $this->url;
	}

	/**
	 * @param string|null $url
	 */
	public function setUrl(?string $url): void
	{
		$this->url = $url;
	}

	/**
	 * @return float|null
	 */
	public function getPrice(): ?float
	{
		return $this->price;
	}

	/**
	 * @param float|null $price
	 */
	public function setPrice(?float $price): void
	{
		$this->price = $price;
	}

	/**
	 * @return Wishlist
	 */
	public function getWishlist(): Wishlist
	{
		return $this->wishlist;
	}

	/**
	 * @return string
	 */
	public function getDescription(): string
	{
		return $this->description;
	}

	/**
	 * @param string $description
	 */
	public function setDescription(string $description): void
	{
		$this->description = $description;
	}

	/**
	 * @param Wishlist $wishlist
	 */
	public function setWishlist(Wishlist $wishlist): void
	{
		$this->wishlist = $wishlist;
	}

	/**
	 * @return bool
	 */
	public function isReserved(): bool
	{
		return $this->reserved;
	}

	/**
	 * @param bool $reserved
	 */
	public function setReserved(bool $reserved): void
	{
		$this->reserved = $reserved;
	}

	#[Pure]
	#[ArrayShape(["id" => "int", "name" => "string", "description" => "string", "url" => "null|string", "image" => "null|string", "price" => "float|null", "reserved" => "bool"])]
	public function toArray(): array
	{
		return [
			"id" => $this->id,
			"name" => $this->name,
			"description" => $this->description,
			"url" => $this->url,
			"image" => $this->image,
			"price" => $this->price,
			"reserved" => $this->reserved,
		];
	}
}
