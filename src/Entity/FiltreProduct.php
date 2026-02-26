<?php

namespace App\Entity;

class FiltreProduct
{
    /**
     * @var string
     */
    private $product;
    private $quantity;

    /**
     * @return string
     */
    public function getProduct(): ?string
    {
        return $this->product;
    }

    /**
     * @param string $product
     * @return FiltreProduct
     */
    public function setProduct(string $product): self
    {
        $this->product = $product;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    /**
     * @param int $quantity
     * @return FiltreProduct
     */
    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;
        return $this;
    }
}
