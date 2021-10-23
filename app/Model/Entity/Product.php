<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Entity\Traits\TId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\ProductRepository")
 * @ORM\Table(name="`products`", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
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
	private string $desc;

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
	 * Many features have one product. This is the owning side.
	 * @ORM\ManyToOne(targetEntity="Wishlist", inversedBy="products")
	 * @ORM\JoinColumn(name="wishlist_id", referencedColumnName="id")
	 */
	private Wishlist $wishlist;

	/**
	 * Many Products have Many Categories.
	 * @ORM\ManyToMany(targetEntity="Category", inversedBy="products")
	 * @ORM\JoinTable(name="products_categories")
	 */
	private Collection $categories;

	#[Pure]
	public function __construct()
	{
		$this->categories = new ArrayCollection();
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

	/**
	 * @return string
	 */
	public function getDesc(): string
	{
		return $this->desc;
	}

	/**
	 * @param string $desc
	 */
	public function setDesc(string $desc): void
	{
		$this->desc = $desc;
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

	public function getCategories(): ArrayCollection|Collection
	{
		return $this->categories;
	}

	public function addCategory(Category $category)
	{
		$this->categories->add($category);
	}

	public function toArray()
	{
		return [
			"name" => $this->getName(),
			"desc" => $this->getDesc(),
			"url" => $this->getUrl(),
			"image" => $this->getImage(),
			"price" => $this->getPrice(),
		];
	}
}
