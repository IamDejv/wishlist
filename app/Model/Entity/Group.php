<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Entity\Traits\TId;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\GroupRepository")
 * @ORM\Table(name="`groups`")
 * @ORM\HasLifecycleCallbacks
 */
class Group extends BaseEntity
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
	 * @ORM\Column(type="GroupType", nullable=false)
	 */
	private string $type;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 */
	private string $image;

	/**
	 * @ORM\Column(type="boolean", nullable=false)
	 */
	private bool $archived;

	/**
	 * @ORM\Column(type="boolean", nullable=false)
	 */
	private bool $active;

	/**
	 * Many features have one product. This is the owning side.
	 * @ORM\ManyToOne(targetEntity="User", inversedBy="myGroups")
	 * @ORM\JoinColumn(name="owner_id", referencedColumnName="id")
	 */
	private User $owner;

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
	 * @return string
	 */
	public function getType(): string
	{
		return $this->type;
	}

	/**
	 * @param string $type
	 */
	public function setType(string $type): void
	{
		$this->type = $type;
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

	#[ArrayShape(["id" => "int", "name" => "string", "description" => "string", "type" => "string", "image" => "string", "active" => "bool", "owner" => "string"])]
	public function toArray(): array
	{
		return [
			"id" => $this->id,
			"name" => $this->name,
			"description" => $this->description,
			"type" => $this->type,
			"image" => $this->image,
			"active" => $this->active,
			"owner" => $this->owner->getId()
		];
	}
}
