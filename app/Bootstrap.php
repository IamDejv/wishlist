<?php

declare(strict_types=1);

namespace App;

use App\Model\Entity\Enums\GroupEnumType;
use App\Model\Entity\Enums\ImageEnumType;
use Doctrine\DBAL\Types\Type;
use Nette\Bootstrap\Configurator;


class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator();
		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory(__DIR__ . '/../temp');
		$configurator->addConfig(__DIR__ . '/config/common.neon');

		if (file_exists(__DIR__ . '/config/local.neon') === true) {
			$configurator->setDebugMode(true);
			$configurator->enableTracy(__DIR__ . '/../log');
			$configurator->enableDebugger(__DIR__ . '/../log');
			$configurator->addConfig(__DIR__ . '/config/local.neon');
		}

		// Doctrine types
		Type::addType("GroupType", GroupEnumType::class);
		Type::addType("ImageType", ImageEnumType::class);

		return $configurator;
	}
}
