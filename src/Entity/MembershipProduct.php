<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: \App\Repository\MembershipProductRepository::class)]
#[UniqueEntity(fields: ['name', 'quantity', 'membership', 'product'], errorPath: 'product', message: 'La quantité de ce produit a déjà été définié dans ce package.', ignoreNull: false)]
class MembershipProduct implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $quantity;

    #[ORM\ManyToOne(targetEntity: CompositionMembershipProductName::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $name;

    #[ORM\ManyToOne(targetEntity: Product::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $product;

    #[ORM\ManyToOne(targetEntity: Membership::class, inversedBy: 'membershipProducts')]
    #[ORM\JoinColumn(nullable: false)]
    private $membership;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?CompositionMembershipProductName
    {
        return $this->name;
    }

    public function setName(?CompositionMembershipProductName $name): self
    {
        $this->name = $name;

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

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getMembership(): ?Membership
    {
        return $this->membership;
    }

    public function setMembership(?Membership $membership): self
    {
        $this->membership = $membership;

        return $this;
    }

    public function isNew(): bool
    {
        return is_null($this->id);
    }

    public function toString(): string
    {
        return "";
    }
}
