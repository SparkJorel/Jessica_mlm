<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\MembershipCostRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class MembershipCost implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(groups={"registration"})
     * @Assert\Positive(groups={"registration"})
     * @Assert\Type(
     *     type="float",
     *     message="La valeur {{ value }} n'est pas un flottant valide",
     *     groups={"registration"}
     * )
     */
    private $value;

    /**
     * @ORM\Column(type="boolean")
     */
    private $state;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Membership")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank(groups={"registration"})
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $membership;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
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
     * @param bool $state
     * @return MembershipCost
     */
    public function setState(bool $state): self
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @ORM\PrePersist()
     * @throws Exception
     */
    public function prePersist(): void
    {
        $this->state = true;
        $this->startedAt = new DateTime("now", new DateTimeZone("Africa/Douala"));
    }

    public function getStartedAt(): ?DateTimeInterface
    {
        return $this->startedAt;
    }

    /**
     * @param DateTimeInterface $startedAt
     * @return MembershipCost
     */
    public function setStartedAt(DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getEndedAt(): ?DateTimeInterface
    {
        return $this->endedAt;
    }

    /**
     * @ORM\PreUpdate()
     * @throws Exception
     */
    public function postUpdate()
    {
        $this->endedAt = new DateTime("now", new DateTimeZone("Africa/Douala"));
    }

    public function toString(): string
    {
        return "";
    }

    public function isNew(): bool
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
