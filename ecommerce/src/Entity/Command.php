<?php

namespace App\Entity;

use App\Repository\CommandRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandRepository::class)
 */
class Command
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=45)
     */
    private $number_command;

    /**
     * @ORM\Column(type="datetime")
     */
    private $command_date;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumberCommand(): ?string
    {
        return $this->number_command;
    }

    public function setNumberCommand(string $number_command): self
    {
        $this->number_command = $number_command;

        return $this;
    }

    public function getCommandDate(): ?\DateTimeInterface
    {
        return $this->command_date;
    }

    public function setCommandDate(\DateTimeInterface $command_date): self
    {
        $this->command_date = $command_date;

        return $this;
    }
}
