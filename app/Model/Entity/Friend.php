<?php

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
	 * @ORM\Column(name="user_id", nullable=true)
	 */
	private string $user;

	/**
	 * @ORM\Id
	 * @ORM\Column(name="friend_user_id", nullable=true)
	 */
	private string $friend;

	/**
	 * @return string
	 */
	public function getUser(): string
	{
		return $this->user;
	}

	/**
	 * @param string $user
	 */
	public function setUser(string $user): void
	{
		$this->user = $user;
	}

	/**
	 * @return string
	 */
	public function getFriend(): string
	{
		return $this->friend;
	}

	/**
	 * @param string $friend
	 */
	public function setFriend(string $friend): void
	{
		$this->friend = $friend;
	}

	#[Pure]
	#[ArrayShape(["friend" => "string", "user" => "string"])]
	public function toArray(): array
	{
		return [
			"friend" => $this->friend,
			"user" => $this->user,
		];
	}
}
