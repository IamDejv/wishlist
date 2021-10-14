<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\UniqueConstraint;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\UserRepository")
 * @ORM\Table(name="`users`", uniqueConstraints={@UniqueConstraint(name="email", columns={"email"})})
 * @ORM\HasLifecycleCallbacks
 */
class User extends BaseEntity
{
	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=false)
	 * @ORM\Id
	 */
	private string $id;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private ?string $firstname;

	/**
	 * @var string|null
	 * @ORM\Column(type="string", nullable=true)
	 */
	private ?string $lastname;

	/**
	 * @var string
	 * @ORM\Column(type="string", nullable=false)
	 */
	private string $email;

	/**
	 * @return string
	 */
	public function getId(): string
	{
		return $this->id;
	}

	/**
	 * @param string $id
	 */
	public function setId(string $id): void
	{
		$this->id = $id;
	}

	/**
	 * @return string|null
	 */
	public function getFirstname(): ?string
	{
		return $this->firstname;
	}

	/**
	 * @param string|null $firstname
	 */
	public function setFirstname(?string $firstname): void
	{
		$this->firstname = $firstname;
	}

	/**
	 * @return string|null
	 */
	public function getLastname(): ?string
	{
		return $this->lastname;
	}

	/**
	 * @param string|null $lastname
	 */
	public function setLastname(?string $lastname): void
	{
		$this->lastname = $lastname;
	}

	/**
	 * @return string
	 */
	public function getEmail(): string
	{
		return $this->email;
	}

	/**
	 * @param string $email
	 */
	public function setEmail(string $email): void
	{
		$this->email = $email;
	}
}
