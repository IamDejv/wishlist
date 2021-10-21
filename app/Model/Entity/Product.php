<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Entity\Traits\TId;
use Doctrine\ORM\Mapping as ORM;

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
	 * Many Products have Many Categories.
	 * @ORM\ManyToMany(targetEntity="Category", inversedBy="products")
	 * @ORM\JoinTable(name="products_categories")
	 */
	private string $category;
}
