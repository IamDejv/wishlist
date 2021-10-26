<?php
declare(strict_types=1);

namespace App\Model\Entity;

use App\Model\Entity\Traits\TId;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

/**
 * @ORM\Entity(repositoryClass="App\Model\Repository\UserRepository")
 * @ORM\Table(name="`users`", uniqueConstraints={@ORM\UniqueConstraint(name="email", columns={"email"})})
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
	 * Many Users have Many Users.
	 * @ORM\ManyToMany(targetEntity="User", mappedBy="myFriends")
	 */
	private Collection $friendsWithMe;

	/**
	 * Many Users have many Users.
	 * @ORM\ManyToMany(targetEntity="User", inversedBy="friendsWithMe")
	 * @ORM\JoinTable(name="friends",
	 *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="friend_user_id", referencedColumnName="id")}
	 *      )
	 */
	private Collection $myFriends;

	/**
	 * Many Users have Many Groups.
	 * @ORM\ManyToMany(targetEntity="Group")
	 * @ORM\JoinTable(name="users_groups",
	 *      joinColumns={@ORM\JoinColumn(name="user_id", referencedColumnName="id")},
	 *      inverseJoinColumns={@ORM\JoinColumn(name="group_id", referencedColumnName="id")}
	 *      )
	 */
	private Collection $groups;

	/**
	 * One product has many features. This is the inverse side.
	 * @ORM\OneToMany(targetEntity="Wishlist", mappedBy="user")
	 */
	private Collection $wishlists;

	#[Pure]
	public function __construct()
	{
		$this->friendsWithMe = new ArrayCollection();
		$this->myFriends = new ArrayCollection();
		$this->groups = new ArrayCollection();
		$this->wishlists = new ArrayCollection();
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

	/**
	 * @return ArrayCollection|Collection
	 */
	public function getFriendsWithMe(): ArrayCollection|Collection
	{
		return $this->friendsWithMe;
	}

	/**
	 * @param ArrayCollection|Collection $friendsWithMe
	 */
	public function setFriendsWithMe(ArrayCollection|Collection $friendsWithMe): void
	{
		$this->friendsWithMe = $friendsWithMe;
	}

	/**
	 * @return ArrayCollection|Collection
	 */
	public function getMyFriends(): ArrayCollection|Collection
	{
		return $this->myFriends;
	}

	/**
	 * @param ArrayCollection|Collection $myFriends
	 */
	public function setMyFriends(ArrayCollection|Collection $myFriends): void
	{
		$this->myFriends = $myFriends;
	}

	/**
	 * @return ArrayCollection|Collection
	 */
	public function getGroups(): ArrayCollection|Collection
	{
		return $this->groups;
	}

	/**
	 * @param ArrayCollection|Collection $groups
	 */
	public function setGroups(ArrayCollection|Collection $groups): void
	{
		$this->groups = $groups;
	}

	/**
	 * @return ArrayCollection|Collection
	 */
	public function getWishlists(): ArrayCollection|Collection
	{
		return $this->wishlists;
	}

	/**
	 * @param ArrayCollection|Collection $wishlists
	 */
	public function setWishlists(ArrayCollection|Collection $wishlists): void
	{
		$this->wishlists = $wishlists;
	}

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

	#[Pure]
	#[ArrayShape(["id" => "string", "email" => "string", "firstname" => "null|string", "lastname" => "null|string"])]
	public function toArray(): array
	{
		return [
			"id" => $this->getId(),
			"email" => $this->getEmail(),
			"firstname" => $this->getFirstname(),
			"lastname" => $this->getLastname(),
		];
	}

	public function addFriend(User $newFriend)
	{
		$this->myFriends->add($newFriend);
	}
}
