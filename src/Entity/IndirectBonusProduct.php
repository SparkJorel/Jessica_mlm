<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\IndirectBonusProductRepository")
 * @UniqueEntity(fields={"product", "value", "lvl"}, groups={"registration_indirect_bonus"}, message="Ces valeurs existent dejà dans la plateforme")
 */
class IndirectBonusProduct implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="Product")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(groups={"registration_indirect_bonus"})
     */
    private $value;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(groups={"registration_indirect_bonus"})
     */
    private $lvl;

    /**
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     * @return IndirectBonusProduct
     */
    public function setProduct(Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    /**
     * @param float|null $value
     * @return IndirectBonusProduct
     */
    public function setValue(?float $value): self
    {
        $this->value = $value;
        return $this;
    }

    public function getLvl(): ?int
    {
        return $this->lvl;
    }

    /**
     * @param int|null $lvl
     * @return IndirectBonusProduct
     */
    public function setLvl(?int $lvl): self
    {
        $this->lvl = $lvl;
        return $this;
    }

    public function __toString() : string
    {
        return $this->getProduct()->getName();
    }

    public function toString() : string
    {
        return $this->__toString();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function isNew(): bool
    {
        return is_null($this->id);
    }
}
