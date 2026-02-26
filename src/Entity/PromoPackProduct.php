<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PromoPackProductRepository")
 */
class PromoPackProduct
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank()
     * @Assert\Positive()
     */
    private $quantity;

    /**
     * @ORM\Column(type="integer")
     * @Assert\PositiveOrZero()
     */
    private $quantityForSV;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;

    /**
     * @var PackPromo
     * @ORM\ManyToOne(targetEntity="App\Entity\PackPromo", inversedBy="products")
     * @Assert\NotBlank()
     */
    private $promo;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

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

    public function getQuantityForSV(): ?int
    {
        return $this->quantityForSV;
    }

    public function setQuantityForSV(int $quantityForSV): self
    {
        $this->quantityForSV = $quantityForSV;

        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;

        return $this;
    }

    /**
     * @return PackPromo
     */
    public function getPromo(): PackPromo
    {
        return $this->promo;
    }

    /**
     * @param PackPromo $promo
     * @return PromoPackProduct
     */
    public function setPromo(PackPromo $promo = null): self
    {
        $this->promo = $promo;
        return $this;
    }
}
