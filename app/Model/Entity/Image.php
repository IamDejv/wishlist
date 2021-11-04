<?php

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\ImageRepository")
 * @ORM\Table(name="`images`", uniqueConstraints={@ORM\UniqueConstraint(name="name", columns={"name"})})
 * @ORM\HasLifecycleCallbacks
 */
class Image extends BaseEntity
{

	/**
	 * @ORM\Id
	 * @ORM\Column(type="string", nullable=false)
	 */
	private string $name;

	/**
	 * @ORM\Column(type="string", nullable=false)
	 */
	private string $path;

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
	public function getPath(): string
	{
		return $this->path;
	}

	/**
	 * @param string $path
	 */
	public function setPath(string $path): void
	{
		$this->path = $path;
	}

	#[ArrayShape(["path" => "string", "name" => "string"])]
	public function toArray(): array
	{
		return [
			"path" => $this->path,
			"name" => $this->name,
		];
	}
}
