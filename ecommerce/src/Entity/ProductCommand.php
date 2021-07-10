<?php

namespace App\Entity;

use App\Repository\ProductCommandRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProductCommandRepository::class)
 */
class ProductCommand
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $product_id;

    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     */
    private $command_id;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    public function getProductId(): ?int
    {
        return $this->product_id;
    }

    public function setProductId(int $product_id): self
    {
        $this->product_id = $product_id;

        return $this;
    }

    public function getCommandId(): ?int
    {
        return $this->command_id;
    }

    public function setCommandId(int $command_id): self
    {
        $this->command_id = $command_id;

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
}
