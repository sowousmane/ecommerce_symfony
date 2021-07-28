<?php

namespace App\Entity;

use App\Repository\CommandRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
    private $date_command;

    /**
     * @ORM\ManyToOne(targetEntity=Client::class, inversedBy="commands")
     * @ORM\JoinColumn(nullable=false)
     */
    private $client;

    /**
     * @ORM\OneToMany(targetEntity=Payment::class, mappedBy="command", orphanRemoval=true)
     */
    private $payments;

    /**
     * @ORM\ManyToOne(targetEntity=CommandProduct::class, inversedBy="command")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commandProduct;

    public function __construct()
    {
        $this->payments = new ArrayCollection();
    }

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

    public function getDateCommand(): ?\DateTimeInterface
    {
        return $this->date_command;
    }

    public function setDateCommand(\DateTimeInterface $date_command): self
    {
        $this->date_command = $date_command;

        return $this;
    }

    public function getClient(): ?Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): self
    {
        $this->client = $client;

        return $this;
    }

 
  

  
    /**
     * @return Collection|Payment[]
     */
    public function getPayments(): Collection
    {
        return $this->payments;
    }

    public function addPayment(Payment $payment): self
    {
        if (!$this->payments->contains($payment)) {
            $this->payments[] = $payment;
            $payment->setCommand($this);
        }

        return $this;
    }

    public function removePayment(Payment $payment): self
    {
        if ($this->payments->removeElement($payment)) {
            // set the owning side to null (unless already changed)
            if ($payment->getCommand() === $this) {
                $payment->setCommand(null);
            }
        }

        return $this;
    }

    public function getCommandProduct(): ?CommandProduct
    {
        return $this->commandProduct;
    }

    public function setCommandProduct(?CommandProduct $commandProduct): self
    {
        $this->commandProduct = $commandProduct;

        return $this;
    }
}
