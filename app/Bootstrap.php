<?php

declare(strict_types=1);

namespace App;

use Nette\Bootstrap\Configurator;


class Bootstrap
{
	public static function boot(): Configurator
	{
		$configurator = new Configurator();
		$configurator->setTimeZone('Europe/Prague');
		$configurator->setTempDirectory(__DIR__ . '/../temp');
		$configurator->addConfig(__DIR__ . '/config/common.neon');
		$configurator->setDebugMode(true);
		$configurator->enableTracy(__DIR__ . '/../log');

		if (file_exists(__DIR__ . '/config/local.neon') === true) {
			$configurator->setDebugMode(true);
			$configurator->enableDebugger(__DIR__ . '/../log');
			$configurator->addConfig(__DIR__ . '/config/local.neon');
		}

		return $configurator;
	}
}
