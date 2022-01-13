<?php

declare(strict_types=1);

use App\Model\Entity\User;
use App\Model\Entity\Wishlist;
use App\Model\EntityManager;
use App\Model\Factory\WishlistFactory;
use App\Model\Hydrator\WishlistHydrator;
use App\Model\Repository\WishlistRepository;
use App\Service\WishlistService;
use App\ValueObject\WishlistValueObject;
use PHPUnit\Framework\TestCase;

class WishlistTest extends TestCase
{
	private WishlistService $service;
	private WishlistHydrator $hydrator;
	private WishlistValueObject $wishlistVo;
	private User $user;

	public function setUp(): void
	{
		$em = $this->createMock(EntityManager::class);
		$repository = $this->createMock(WishlistRepository::class);
		$this->hydrator = new WishlistHydrator();
		$factory = $this->createMock(WishlistFactory::class);

		$this->wishlistVo = new WishlistValueObject();
		$this->wishlistVo->name = "Test Wishlist";
		$this->wishlistVo->image = "/example/image";
		$this->wishlistVo->user = "random-firebase-uid";

		$this->user = new User();
		$this->user->setId("random-firebase-uid");
		$this->user->setEmail("david@example.com");
		$this->user->setLastname("Kalianko");
		$this->user->setFirstname("David");

		$wishlist = new Wishlist();
		$wishlist->setName("Test Wishlist");
		$wishlist->setActive(true);
		$wishlist->setArchived(false);
		$wishlist->setImage("/example/image");
		$wishlist->setId(2);
		$wishlist->setOwner($this->user);

		$factory->method("create")->willReturn($wishlist);
		$repository->method("find")->with(2)->willReturn($wishlist);

		$this->service = new WishlistService($em, $repository, $this->hydrator, $factory);
	}

	public function testHydrate()
	{
		$entity = $this->hydrator->hydrate($this->wishlistVo, null);

		self::assertInstanceOf(Wishlist::class, $entity);

		$this->assertFalse($entity->isActive());
		$this->assertFalse($entity->isArchived());
	}

	public function testCreate()
	{
		$entity = $this->service->create($this->wishlistVo, $this->user);

		self::assertInstanceOf(Wishlist::class, $entity);

		self::assertInstanceOf(User::class, $entity->getOwner());
	}

	public function testUpdate()
	{
		$this->wishlistVo->name = "Example";
		$entity = $this->service->update(2, $this->wishlistVo);

		self::assertInstanceOf(Wishlist::class, $entity);

		self::assertEquals("Example", $entity->getName());
	}
}
