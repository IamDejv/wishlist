<?php
declare(strict_types=1);

namespace App\Util;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\DomCrawler\Crawler;

class LinkAnalyzer
{

	#[ArrayShape(["image" => "null|string", "name" => "string", "description" => "null|string"])]
	public function analyze(string $link): array
	{
		$file = file_get_contents($link);

		$crawler = new Crawler($file);

		try {
			$title = $crawler->filter("meta[property='og:title']")->first()->html();
		} catch (\Exception $e) {}

		if (empty($title)) {
			try {
				$title = $crawler->filter("head title")->first()->html();
			} catch (\Exception $e) {}
		}

		try {
			$description = $crawler->filter("[itemprop='description']")->first()->html();
		} catch (\Exception $e) {}

		if (empty($description)) {
			try {
				$description = $crawler->filter("meta[property='og:description']")->attr("content");
			} catch (\Exception $e) {}
		}


		if (empty($description)) {
			$description = $crawler->filter("meta[name='og:description']")->attr("content");
		}

		if (empty($description)) {
			$description = $crawler->filter("meta[name='description']")->attr("content");
		}

		$images = [];
		try {
			$images[] = $crawler->filter("meta[property='og:image']")->attr("content");
		} catch (\Exception $e) {}
		try {
			$images[]= $crawler->filter("link[as='image']")->attr("href");
		} catch (\Exception $e) {}
		try {
			$images[]= $crawler->filter("link[rel='icon']")->attr("href");
		} catch (\Exception $e) {}
		try {
			$images[]= $crawler->filter("link[rel='previewimage']")->attr("href");
		} catch (\Exception $e) {}
		try {
			$images[]= $crawler->filter("img[itemprop='image']")->attr("src");
		} catch (\Exception $e) {}

		return [
			"image" => $images ?? null,
			"name" => $title ?? null,
			"description" => $description ?? null,
		];
	}
}
