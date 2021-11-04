<?php
declare(strict_types=1);

namespace App\Fixtures;

use App\Model\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class UserFixture implements FixtureInterface
{

	public function load(ObjectManager $manager)
	{
		$userA = new User();
		$userA->setId("QzLHjpAxzgdbqdMX0wGkX06PEXg2");
		$userA->setFirstname("David");
		$userA->setLastname("Kalianko");
		$userA->setEmail("kaliankodavid@gmail.com");

		$userB = new User();
		$userB->setId("3b8hpKNWCQbnvx913gXQKmFLBO22");
		$userB->setFirstname("Darina");
		$userB->setLastname("Plachetkova");
		$userB->setEmail("darina@example.com");

		$userC = new User();
		$userC->setId("iP3qP7GUDOTIYeKkEyFjaf9fDiQ2");
		$userC->setFirstname("Jakub");
		$userC->setLastname("Holoubek");
		$userC->setEmail("jakub@example.com");

		$userD = new User();
		$userD->setId("OVW4Ud2micQ72uSD57KLIfGR1Tx1");
		$userD->setFirstname('Karel');
		$userD->setLastname("Jirsak");
		$userD->setEmail("kaja@example.com");

		$userE = new User();
		$userE->setId("WseVBIWKYQbaQyM5fQwZKVaq3JH2");
		$userE->setFirstname("Dejv");
		$userE->setLastname("Kalic");
		$userE->setEmail("david.kalianko@quanti.cz");

		$manager->persist($userA);
		$manager->persist($userB);
		$manager->persist($userC);
		$manager->persist($userD);
		$manager->persist($userE);
		$manager->flush();
	}
}
