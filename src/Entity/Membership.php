<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\MembershipRepository::class)]
#[UniqueEntity(fields: ['coefficent'], groups: ['registration_membership'])]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
class Membership implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Assert\NotBlank(groups: ['registration_membership'])]
    private $code;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(groups: ['registration_membership'])]
    private $name;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(groups: ['registration_membership'])]
    private $coefficent;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(groups: ['registration_membership'])]
    private $membershipCost;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(groups: ['registration_membership'])]
    private $membershipGroupeSV;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(groups: ['registration_membership'])]
    private $membershipProductCote;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(groups: ['registration_membership'])]
    private $membershipBonusBinairePourcent;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\OneToMany(targetEntity: MembershipProduct::class, mappedBy: 'membership', orphanRemoval: true)]
    private $membershipProducts;

    public function __construct()
    {
        $this->membershipProducts = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;
        return $this;
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

    /**
     * @return int
     */
    public function getCoefficent(): ?int
    {
        return $this->coefficent;
    }

    /**
     * @param int $coefficent
     * @return Membership
     */
    public function setCoefficent($coefficent): self
    {
        $this->coefficent = $coefficent;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     * @return Membership
     */
    public function setDescription($description): self
    {
        $this->description = $description;
        return $this;
    }

    public function toString(): string
    {
        return $this->__toString();
    }

    public function isNew(): bool
    {
        return is_null($this->id);
    }

    public function __toString(): string
    {
        return $this->code;
    }

    public function getMembershipCost(): ?float
    {
        return $this->membershipCost;
    }

    public function setMembershipCost(?float $membershipCost): self
    {
        $this->membershipCost = $membershipCost;

        return $this;
    }

    public function getMembershipGroupeSV(): ?float
    {
        return $this->membershipGroupeSV;
    }

    public function setMembershipGroupeSV(?float $membershipGroupeSV): self
    {
        $this->membershipGroupeSV = $membershipGroupeSV;

        return $this;
    }

    public function getMembershipProductCote(): ?float
    {
        return $this->membershipProductCote;
    }

    public function setMembershipProductCote(?float $membershipProductCote): self
    {
        $this->membershipProductCote = $membershipProductCote;

        return $this;
    }

    public function getMembershipBonusBinairePourcent(): ?float
    {
        return $this->membershipBonusBinairePourcent;
    }

    public function setMembershipBonusBinairePourcent(?float $membershipBonusBinairePourcent): self
    {
        $this->membershipBonusBinairePourcent = $membershipBonusBinairePourcent;

        return $this;
    }

    /**
     * @return Collection|MembershipProduct[]
     */
    public function getMembershipProducts(): Collection
    {
        return $this->membershipProducts;
    }

    public function addMembershipProduct(MembershipProduct $membershipProduct): self
    {
        if (!$this->membershipProducts->contains($membershipProduct)) {
            $this->membershipProducts[] = $membershipProduct;
            $membershipProduct->setMembership($this);
        }

        return $this;
    }

    public function removeMembershipProduct(MembershipProduct $membershipProduct): self
    {
        if ($this->membershipProducts->contains($membershipProduct)) {
            $this->membershipProducts->removeElement($membershipProduct);
            // set the owning side to null (unless already changed)
            if ($membershipProduct->getMembership() === $this) {
                $membershipProduct->setMembership(null);
            }
        }

        return $this;
    }
}
