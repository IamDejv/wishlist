<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\FriendRepository")
 * @ORM\Table(name="`friends`")
 * @ORM\HasLifecycleCallbacks
 */
class Friend extends BaseEntity
{
	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
	 */
	private User $user;

	/**
	 * @ORM\Id
	 * @ORM\ManyToOne(targetEntity="User")
	 * @ORM\JoinColumn(name="friend_id", referencedColumnName="id", nullable=false)
	 */
	private User $friend;

	/**
	 * @ORM\Column(type="boolean")
	 */
	private bool $confirmed;

	/**
	 * @return User
	 */
	public function getUser(): User
	{
		return $this->user;
	}

	/**
	 * @param User $user
	 */
	public function setUser(User $user): void
	{
		$this->user = $user;
	}

	/**
	 * @return User
	 */
	public function getFriend(): User
	{
		return $this->friend;
	}

	/**
	 * @param User $friend
	 */
	public function setFriend(User $friend): void
	{
		$this->friend = $friend;
	}

	/**
	 * @return bool
	 */
	public function isConfirmed(): bool
	{
		return $this->confirmed;
	}

	/**
	 * @param bool $confirmed
	 */
	public function setConfirmed(bool $confirmed): void
	{
		$this->confirmed = $confirmed;
	}

	#[Pure]
	#[ArrayShape(["user" => "string", "friend" => "string", "confirmed" => "bool"])]
	public function toArray(): array
	{
		return [
			"user" => $this->user->getId(),
			"friend" => $this->friend->getId(),
			"confirmed" => $this->confirmed,
		];
	}
}
