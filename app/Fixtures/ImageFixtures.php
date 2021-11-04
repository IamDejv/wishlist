<?php

namespace App\Fixtures;

use App\Model\Entity\Image;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Persistence\ObjectManager;

class ImageFixtures implements FixtureInterface
{

	/**
	 * @inheritDoc
	 */
	public function load(ObjectManager $manager)
	{
		$imageA = new Image();
		$imageA->setName("balloons-1");
		$imageA->setPath("/public/assets/balloons-1.jpg");

		$imageB = new Image();
		$imageB->setName("birthday-1");
		$imageB->setPath("/public/assets/birthday-1.jpg");

		$imageC = new Image();
		$imageC->setName("birthday-2");
		$imageC->setPath("/public/assets/birthday-2.jpg");

		$imageD = new Image();
		$imageD->setName("birthday-3");
		$imageD->setPath("/public/assets/birthday-3.jpg");

		$imageE = new Image();
		$imageE->setName("christmas-1");
		$imageE->setPath("/public/assets/christmas-1.jpg");

		$imageF = new Image();
		$imageF->setName("christmas-2");
		$imageF->setPath("/public/assets/christmas-2.jpg");

		$imageG = new Image();
		$imageG->setName("christmas-3");
		$imageG->setPath("/public/assets/christmas-3.jpg");

		$imageH = new Image();
		$imageH->setName("christmas-4");
		$imageH->setPath("/public/assets/christmas-4.jpg");

		$imageI = new Image();
		$imageI->setName("christmas-5");
		$imageI->setPath("/public/assets/christmas-5.jpg");

		$imageJ = new Image();
		$imageJ->setName("christmas-6");
		$imageJ->setPath("/public/assets/christmas-6.jpg");

		$imageK = new Image();
		$imageK->setName("christmas-tree-1");
		$imageK->setPath("/public/assets/christmas-tree-1.jpg");

		$imageL = new Image();
		$imageL->setName("cookies-1");
		$imageL->setPath("/public/assets/cookies-1.jpg");

		$imageM = new Image();
		$imageM->setName("easter-1");
		$imageM->setPath("/public/assets/easter-1.jpg");

		$imageN = new Image();
		$imageN->setName("easter-2");
		$imageN->setPath("/public/assets/easter-2.jpg");

		$imageO = new Image();
		$imageO->setName("gift-1");
		$imageO->setPath("/public/assets/gift-1.jpg");

		$imageP = new Image();
		$imageP->setName("gift-2");
		$imageP->setPath("/public/assets/gift-2.jpg");

		$imageQ = new Image();
		$imageQ->setName("pink-1");
		$imageQ->setPath("/public/assets/pink-1.jpg");

		$imageR = new Image();
		$imageR->setName("pumpkin-1");
		$imageR->setPath("/public/assets/pumpkin-1.jpg");

		$imageS = new Image();
		$imageS->setName("pumpkin-2");
		$imageS->setPath("/public/assets/pumpkin-2.jpg");

		$imageT = new Image();
		$imageT->setName("red-1");
		$imageT->setPath("/public/assets/red-1.jpg");

		$imageU = new Image();
		$imageU->setName("shell-1");
		$imageU->setPath("/public/assets/shell-1.jpg");

		$manager->persist($imageA);
		$manager->persist($imageB);
		$manager->persist($imageC);
		$manager->persist($imageD);
		$manager->persist($imageE);
		$manager->persist($imageF);
		$manager->persist($imageG);
		$manager->persist($imageH);
		$manager->persist($imageI);
		$manager->persist($imageJ);
		$manager->persist($imageK);
		$manager->persist($imageL);
		$manager->persist($imageM);
		$manager->persist($imageN);
		$manager->persist($imageO);
		$manager->persist($imageP);
		$manager->persist($imageQ);
		$manager->persist($imageR);
		$manager->persist($imageS);
		$manager->persist($imageT);
		$manager->persist($imageU);

		$manager->flush();
	}
}
