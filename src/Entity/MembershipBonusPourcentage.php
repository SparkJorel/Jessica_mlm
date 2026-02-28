<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\MembershipBonusPourcentageRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
class MembershipBonusPourcentage implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    #[Assert\Positive(groups: ['registration'])]
    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\Type(type: 'float', message: 'La valeur {{ value }} n\'est pas un flottant valide', groups: ['registration'])]
    private $value;

    #[ORM\Column(type: 'boolean')]
    private $state;

    #[ORM\Column(type: 'datetime')]
    private $startedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $endedAt;

    #[ORM\ManyToOne(targetEntity: Membership::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank(groups: ['registration'])]
    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
    private $membership;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getState(): ?bool
    {
        return $this->state;
    }

    /**
     * @throws Exception
     */
    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->state = true;
        $this->startedAt = new DateTime("now", new DateTimeZone("Africa/Douala"));
    }

    /**
     * @throws Exception
     */
    #[ORM\PreUpdate]
    public function postUpdate()
    {
        $this->endedAt = new DateTime("now", new DateTimeZone("Africa/Douala"));
    }

    /**
     * @param bool $state
     * @return MembershipBonusPourcentage
     */
    public function setState(bool $state): self
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @param DateTimeInterface $startedAt
     * @return MembershipBonusPourcentage
     */
    public function setStartedAt(DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getStartedAt(): ?DateTimeInterface
    {
        return $this->startedAt;
    }

    public function getEndedAt(): ?DateTimeInterface
    {
        return $this->endedAt;
    }

    public function toString(): string
    {
        return "";
    }

    public function isNew() : bool
    {
        return is_null($this->id);
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
}
