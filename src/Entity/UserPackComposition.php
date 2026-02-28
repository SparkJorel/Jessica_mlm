<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\UserPackCompositionRepository::class)]
class UserPackComposition implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: CompositionMembershipProductName::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $packName;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $upgraded;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getPackName(): ?CompositionMembershipProductName
    {
        return $this->packName;
    }

    public function setPackName(?CompositionMembershipProductName $packName): self
    {
        $this->packName = $packName;

        return $this;
    }

    public function getUpgraded(): ?bool
    {
        return $this->upgraded;
    }

    public function setUpgraded(?bool $upgraded): self
    {
        $this->upgraded = $upgraded;

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
