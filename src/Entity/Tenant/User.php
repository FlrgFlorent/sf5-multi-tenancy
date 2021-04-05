<?php

namespace App\Entity\Tenant;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="App\Repository\Tenant\UserRepository")
 */
class User
{

	/**
	 * @ORM\Id()
	 * @ORM\GeneratedValue()
	 * @ORM\Column(type="integer", options={"unsigned"=true})
	 */
	private ?int $id;

	/**
	 * @ORM\Column(type="string", length=255)
	 */
	private ?string $name;

	public function getId(): ?int
	{
		return $this->id;
	}

	public function getName(): ?string
	{
		return $this->name;
	}

	public function setName(string $name): self
	{
		$this->name = $name;

		return $this;
	}
}