<?php

namespace App\Entity;

use App\Repository\CommandProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CommandProductRepository::class)
 */
class CommandProduct
{  
    /**
    * @ORM\Id
    * @ORM\GeneratedValue
    * @ORM\Column(type="integer")
    */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $command_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $product_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\OneToMany(targetEntity=Product::class, mappedBy="commandProduct", orphanRemoval=true)
     */
    private $product;

    /**
     * @ORM\OneToMany(targetEntity=Command::class, mappedBy="commandProduct", orphanRemoval=true)
     */
    private $command;

    public function __construct()
    {
        $this->product = new ArrayCollection();
        $this->command = new ArrayCollection();
    }
    
    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommandId(): ?int
    {
        return $this->command_id;
    }

    public function setCommandId(Command $command_id): self
    {
        $this->command_id = $command_id;

        return $this;
    }

    public function getProductId(): ?int
    {
        return $this->product_id;
    }

    public function setProductId(Product $product_id): self
    {
        $this->product_id = $product_id;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * @return Collection|Product[]
     */
    public function getProduct(): Collection
    {
        return $this->product;
    }

    public function addProduct(Product $product): self
    {
        if (!$this->product->contains($product)) {
            $this->product[] = $product;
            $product->setCommandProduct($this);
        }

        return $this;
    }

    public function removeProduct(Product $product): self
    {
        if ($this->product->removeElement($product)) {
            // set the owning side to null (unless already changed)
            if ($product->getCommandProduct() === $this) {
                $product->setCommandProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Command[]
     */
    public function getCommand(): Collection
    {
        return $this->command;
    }

    public function addCommand(Command $command): self
    {
        if (!$this->command->contains($command)) {
            $this->command[] = $command;
            $command->setCommandProduct($this);
        }

        return $this;
    }

    public function removeCommand(Command $command): self
    {
        if ($this->command->removeElement($command)) {
            // set the owning side to null (unless already changed)
            if ($command->getCommandProduct() === $this) {
                $command->setCommandProduct(null);
            }
        }

        return $this;
    }
}
