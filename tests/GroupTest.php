<?php

declare(strict_types=1);

use App\Model\Entity\Enums\GroupEnumType;
use App\Model\Entity\Group;
use App\Model\Entity\User;
use App\Model\EntityManager;
use App\Model\Factory\GroupFactory;
use App\Model\Hydrator\GroupHydrator;
use App\Model\Repository\GroupRepository;
use App\Service\GroupService;
use App\Service\UserService;
use App\ValueObject\ActionUserValueObject;
use App\ValueObject\GroupValueObject;
use PHPUnit\Framework\TestCase;

class GroupTest extends TestCase
{
	private GroupService $service;
	private GroupHydrator $hydrator;
	private GroupValueObject $groupVo;
	private User $user;

	public function setUp(): void
	{
		$em = $this->createMock(EntityManager::class);
		$repository = $this->createMock(GroupRepository::class);
		$this->hydrator = new GroupHydrator();
		$factory = $this->createMock(GroupFactory::class);
		$userService = $this->createMock(UserService::class);

		$this->groupVo = new GroupValueObject();
		$this->groupVo->name = "Example Name";
		$this->groupVo->description = "Description";
		$this->groupVo->type = GroupEnumType::BASIC;
		$this->groupVo->image = "/example/image";

		$group = new Group();
		$group->setId(15);
		$group->setName("Test Name");
		$group->setDescription("Description");
		$group->setImage("/example/image");
		$group->setArchived(false);
		$group->setActive(false);
		$group->setType(GroupEnumType::BASIC);

		$this->user = new User();
		$this->user->setId("random-firebase-uid");
		$this->user->setEmail("david@example.com");
		$this->user->setLastname("Kalianko");
		$this->user->setFirstname("David");

		$factory->method("create")->willReturn($group);
		$repository->method("find")->with(2)->willReturn($group);
		$userService->method("getById")->with("random-firebase-uid")->willReturn($this->user);

		$this->service = new GroupService($em, $repository, $this->hydrator, $factory, $userService);
	}

	public function testHydrate()
	{
		$entity = $this->hydrator->hydrate($this->groupVo, null);

		self::assertInstanceOf(Group::class, $entity);

		$this->assertFalse($entity->isActive());
		$this->assertFalse($entity->isArchived());
	}

	public function testCreate()
	{
		$entity = $this->service->create($this->groupVo, $this->user);

		self::assertInstanceOf(Group::class, $entity);

		self::assertNotEmpty($this->user->getGroups());

		self::assertEquals($this->user->getId(), $entity->getOwner()->getId());
	}

	public function testUpdate()
	{
		$this->groupVo->setName("Random name");
		$entity = $this->service->update(2, $this->groupVo);

		self::assertInstanceOf(Group::class, $entity);

		self::assertEquals("Random name", $entity->getName());
	}

	public function testAction()
	{
		$vo = new ActionUserValueObject();
		$vo->id = "random-firebase-uid";
		$vo->action = "addToGroup";
		$entity = $this->service->actionUser(2, $vo);

		self::assertNotEmpty($entity->getGroups());

		$vo->action = "removeFromGroup";
		$entity = $this->service->actionUser(2, $vo);

		self::assertEmpty($entity->getGroups());
	}
}
