<?php

declare(strict_types=1);

use Apitte\Core\Exception\Api\ClientErrorException;
use App\Model\Entity\Product;
use App\Model\Entity\User;
use App\Model\Entity\Wishlist;
use App\Model\EntityManager;
use App\Model\Factory\ProductFactory;
use App\Model\Hydrator\ProductHydrator;
use App\Model\Repository\ProductRepository;
use App\Model\Repository\WishlistRepository;
use App\Service\ProductService;
use App\ValueObject\ProductValueObject;
use App\ValueObject\UpdateProductValueObject;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
	private ProductService $service;
	private ProductHydrator $hydrator;
	private ProductValueObject $productVo;

	public function setUp(): void
	{
		$em = $this->createMock(EntityManager::class);
		$repository = $this->createMock(ProductRepository::class);
		$wishlistRepository = $this->createMock(WishlistRepository::class);
		$this->hydrator = new ProductHydrator($wishlistRepository);
		$factory = $this->createMock(ProductFactory::class);

		$this->productVo = new ProductValueObject();
		$this->productVo->name = "Example Name";
		$this->productVo->description = "Description";
		$this->productVo->image = "/example/image";
		$this->productVo->url = "url";
		$this->productVo->price = 250;
		$this->productVo->wishlistId = 2;

		$user = new User();
		$user->setId("random-firebase-uid");
		$user->setEmail("david@example.com");
		$user->setLastname("Kalianko");
		$user->setFirstname("David");

		$wishlist = new Wishlist();
		$wishlist->setName("Test Wishlist");
		$wishlist->setActive(true);
		$wishlist->setArchived(false);
		$wishlist->setImage("example/image");
		$wishlist->setId(2);
		$wishlist->setOwner($user);

		$product = new Product();
		$product->setId(15);
		$product->setName("Test Name");
		$product->setDescription("Description");
		$product->setImage("/example/image");
		$product->setPrice(250);
		$product->setWishlist($wishlist);
		$product->setReserved(false);

		$factory->method("create")->willReturn($product);
		$repository->method("find")->with(2)->willReturn($product);
		$wishlistRepository->method("find")->with(2)->willReturn($wishlist);

		$this->service = new ProductService($em, $repository, $this->hydrator, $factory);
	}

	public function testHydrate()
	{
		$entity = $this->hydrator->hydrate($this->productVo, null);

		self::assertInstanceOf(Product::class, $entity);

		$this->assertFalse($entity->isReserved());
		$this->assertInstanceOf(Wishlist::class, $entity->getWishlist());
	}

	public function testCreate()
	{
		$entity = $this->service->create($this->productVo);

		self::assertInstanceOf(Product::class, $entity);
	}

	public function testUpdate()
	{
		$productVo = new UpdateProductValueObject();
		$productVo->name = "Random Name";
		$productVo->description = "Description";
		$productVo->image = "/example/image";
		$productVo->url = "url";
		$productVo->price = 250;

		$entity = $this->service->update(2, $productVo);

		self::assertInstanceOf(Product::class, $entity);

		self::assertEquals("Random Name", $entity->getName());
	}

	public function testReserve()
	{
		$user = new User();
		$user->setId("random-firebase-uidd");
		$user->setEmail("david@example.com");
		$user->setLastname("Kalianko");
		$user->setFirstname("David");

		$entity = $this->service->reserve(2, $user);

		self::assertTrue($entity->isReserved());
	}

	public function testReserveByOwner()
	{
		$user = new User();
		$user->setId("random-firebase-uid");
		$user->setEmail("david@example.com");
		$user->setLastname("Kalianko");
		$user->setFirstname("David");

		self::expectException(ClientErrorException::class);
		$this->service->reserve(2, $user);
	}
}
