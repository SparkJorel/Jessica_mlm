<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\IndirectBonusMembershipRepository::class)]
#[UniqueEntity(fields: ['membership', 'value', 'lvl'], groups: ['registration_indirect_bonus_mbship'], message: 'Ces valeurs existent dejà dans la plateforme')]
class IndirectBonusMembership implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Membership::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $membership;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(groups: ['registration_indirect_bonus_mbship'])]
    private $value;

    #[ORM\Column(type: 'integer')]
    #[Assert\NotBlank(groups: ['registration_indirect_bonus_mbship'])]
    private $lvl;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getLvl(): ?int
    {
        return $this->lvl;
    }

    public function setLvl(int $lvl): self
    {
        $this->lvl = $lvl;

        return $this;
    }

    public function isNew() : bool
    {
        return is_null($this->id);
    }

    public function toString(): string
    {
        return "";
    }
}
