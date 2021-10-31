<?php
declare(strict_types=1);

namespace App\Model\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait TId
{
	/**
	 * @var int
	 * @ORM\Column(type="integer", nullable=false)
	 * @ORM\Id
	 * @ORM\GeneratedValue
	 */
	private int $id;

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId(int $id): void
	{
		$this->id = $id;
	}
}
