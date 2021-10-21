<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Entity\Traits\TId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\CategoryRepository")
 * @ORM\Table(name="`categories`", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\HasLifecycleCallbacks
 */
class Category extends BaseEntity
{
	use TId;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 */
	private string $name;

	/**
	 * @ORM\Column(type="string", nullable=true)
	 */
	private ?string $image;

	/**
	 * Many Categories have One Category.
	 * @ORM\ManyToOne(targetEntity="Category", inversedBy="subcategories")
	 * @ORM\JoinColumn(name="parent_id", referencedColumnName="id")
	 */
	private Category $parent;

	/**
	 * One Category has Many Categories.
	 * @ORM\OneToMany(targetEntity="Category", mappedBy="parent")
	 */
	private Collection $subcategories;

	#[Pure]
	public function __construct()
	{
		$this->subcategories = new ArrayCollection();
	}


}
