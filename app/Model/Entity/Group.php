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

	#[ArrayShape(["id" => "int", "name" => "string", "description" => "string", "type" => "string", "image" => "string", "active" => "bool"])]
	#[Pure]
	public function toArray(): array
	{
		return [
			"id" => $this->getId(),
			"name" => $this->getName(),
			"description" => $this->getDescription(),
			"type" => $this->getType(),
			"image" => $this->getImage(),
			"active" => $this->isActive(),
		];
	}
}
