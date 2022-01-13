<?php

use App\Model\Entity\Enums\GroupEnumType;
use App\Model\Entity\Group;
use App\Model\Entity\User;
use App\Model\Entity\Wishlist;
use App\Model\EntityManager;
use App\Model\Factory\UserFactory;
use App\Model\Hydrator\UserHydrator;
use App\Model\Repository\FriendRepository;
use App\Model\Repository\GroupRepository;
use App\Model\Repository\UserRepository;
use App\Model\Repository\WishlistRepository;
use App\Service\UserService;
use App\ValueObject\ActionGroupValueObject;
use App\ValueObject\ActionWishlistValueObject;
use App\ValueObject\UserValueObject;
use Doctrine\Common\Collections\ArrayCollection;
use Kreait\Firebase\Auth;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
	private UserService $service;
	private UserHydrator $hydrator;
	private UserValueObject $userVo;

	public function setUp(): void
	{
		$em = $this->createMock(EntityManager::class);
		$repository = $this->createMock(UserRepository::class);
		$friendRepository = $this->createMock(FriendRepository::class);
		$wishlistRepository = $this->createMock(WishlistRepository::class);
		$groupRepository = $this->createMock(GroupRepository::class);
		$auth = $this->createMock(Auth::class);
		$this->hydrator = new UserHydrator();
		$factory = $this->createMock(UserFactory::class);

		$this->userVo = new UserValueObject();
		$this->userVo->firstname = "David";
		$this->userVo->lastname = "Kalianko";
		$this->userVo->email = "david@example.com";

		$user = new User();
		$user->setId("random-firebase-uid");
		$user->setEmail("david@example.com");
		$user->setLastname("Kalianko");
		$user->setFirstname("David");

		$wishlist = new Wishlist();
		$wishlist->setId(2);
		$wishlist->setName("Test Wishlist");
		$wishlist->setActive(false);
		$wishlist->setArchived(false);
		$wishlist->setImage("example/image");
		$wishlist->setOwner($user);

		$group = new Group();
		$group->setId(2);
		$group->setName("Test Name");
		$group->setDescription("Description");
		$group->setImage("/example/image");
		$group->setActive(true);
		$group->setArchived(false);
		$group->setOwner($user);

		$user->setWishlists(new ArrayCollection([$wishlist]));

		$user->setGroups(new ArrayCollection([$group]));

		$friend = new User();
		$friend->setId("random-friend-firebase-uid");
		$friend->setEmail("david@example.com");
		$friend->setLastname("Kalianko");
		$friend->setFirstname("David");

		$group->setType(GroupEnumType::BASIC);

		$factory->method("create")->willReturn($user);
		$repository->method("find")->with("random-firebase-uid")->willReturn($user);
		$friendRepository->method("find")->with("random-friend-firebase-uid")->willReturn($friend);

		$wishlistRepository->method("find")->with(2)->willReturn($wishlist);
		$groupRepository->method("find")->with(2)->willReturn($group);

		$this->service = new UserService(
			$em,
			$repository,
			$this->hydrator,
			$auth,
			$factory,
			$friendRepository,
			$wishlistRepository,
			$groupRepository
		);
	}

	public function testHydrate()
	{
		$entity = $this->hydrator->hydrate($this->userVo);

		self::assertInstanceOf(User::class, $entity);
	}

	public function testUpdate()
	{
		$this->userVo->firstname = "Dejv";

		$entity = $this->service->update("random-firebase-uid", $this->userVo);

		self::assertInstanceOf(User::class, $entity);

		self::assertEquals("Dejv", $entity->getFirstname());
	}

	public function testUpdateMe()
	{
		$this->userVo->firstname = "Dejvid";

		$entity = $this->service->updateMe("random-firebase-uid", $this->userVo);

		self::assertInstanceOf(User::class, $entity);

		self::assertEquals("Dejvid", $entity->getFirstname());
	}

	public function testAddAndRemoveFriend()
	{
		$entity = $this->service->addFriend("random-firebase-uid", "random-friend-firebase-uid");

		self::assertNotEmpty($entity->getFriendsWithMe());

		$this->service->confirmFriend("random-firebase-uid", "random-friend-firebase-uid");

		$entity->getFriendsWithMe();

		$entity = $this->service->removeFriend("random-firebase-uid", "random-friend-firebase-uid");

		self::assertEmpty($entity->getFriendsWithMe());
	}

	public function testActionGroup()
	{
		$vo = new ActionGroupValueObject();
		$vo->id = 2;
		$vo->action = "setActive";
		$group = $this->service->actionGroup("random-firebase-uid", $vo);

		self::assertTrue($group->isActive());

		$vo->action = "setInactive";
		$this->service->actionGroup("random-firebase-uid", $vo);

		self::assertFalse($group->isActive());
	}

	public function testActionWishlist()
	{
		$vo = new ActionWishlistValueObject();
		$vo->id = 2;
		$vo->action = "setActive";
		$wishlist = $this->service->actionWishlist("random-firebase-uid", $vo);

		self::assertTrue($wishlist->isActive());

		$vo->action = "setInactive";
		$wishlist = $this->service->actionWishlist("random-firebase-uid", $vo);

		self::assertFalse($wishlist->isActive());
	}
}
