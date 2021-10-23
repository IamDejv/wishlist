<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Entity\Traits\TId;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\GroupRepository")
 * @ORM\Table(name="`groups`", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
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
	 * @ORM\Column(type="boolean")
	 */
	private bool $public;

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
	 * @return bool
	 */
	public function isPublic(): bool
	{
		return $this->public;
	}

	/**
	 * @param bool $public
	 */
	public function setPublic(bool $public): void
	{
		$this->public = $public;
	}

	#[Pure]
	#[ArrayShape(["id" => "int", "name" => "string", "description" => "string", "type" => "string", "public" => "bool"])]
	public function toArray(): array
	{
		return [
			"id" => $this->getId(),
			"name" => $this->getName(),
			"description" => $this->getDescription(),
			"type" => $this->getType(),
			"public" => $this->isPublic(),
		];
	}
}
