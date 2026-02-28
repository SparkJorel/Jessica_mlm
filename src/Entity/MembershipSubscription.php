<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

#[ORM\Entity(repositoryClass: \App\Repository\MembershipSubscriptionRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['member', 'membership'])]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
class MembershipSubscription implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
    private $member;

    #[ORM\ManyToOne(targetEntity: Membership::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
    private $membership;

    #[ORM\Column(type: 'boolean')]
    private $state;

    #[ORM\Column(type: 'boolean', nullable: true)]
    private $upgraded;

    /**
     * @var boolean
     */
    #[ORM\Column(type: 'boolean')]
    private $paid;

    /**
     * @var DateTime
     */
    #[ORM\Column(type: 'datetime', nullable: true)]
    private $paidAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $startedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $endedAt;

    #[ORM\Column(type: 'float', nullable: true)]
    private $price;

    #[ORM\Column(type: 'float', nullable: true)]
    private $totalSVBinaire;

    #[ORM\ManyToOne(targetEntity: User::class)]
    private $createdBy;

    private $upgradable;

    private $membershipUp = array();

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;
        return $this;
    }

    public function getMember(): ?User
    {
        return $this->member;
    }

    public function setMember(?User $member): self
    {
        $this->member = $member;
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

    public function getState(): ?bool
    {
        return $this->state;
    }

    /**
     * @param bool $state
     * @return MembershipSubscription
     */
    public function setState(bool  $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function isUpgraded(): ?bool
    {
        return $this->upgraded;
    }

    public function setUpgraded(bool $upgraded): self
    {
        $this->upgraded = $upgraded;
        return $this;
    }

    public function getEndedAt(): ?DateTimeInterface
    {
        return $this->endedAt;
    }

    /**
     * @param DateTime $endedAt
     * @return MembershipSubscription
     */
    public function setEndedAt($endedAt): self
    {
        $this->endedAt = $endedAt;
        return $this;
    }

    public function getStartedAt(): ?DateTimeInterface
    {
        return $this->startedAt;
    }

    /**
     * @param DateTimeInterface $startedAt
     * @return MembershipSubscription
     */
    public function setStartedAt(DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function toString(): string
    {
        return "";
    }

    public function isNew(): bool
    {
        return is_null($this->id);
    }

    /**
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->paid;
    }

    /**
     * @param bool $paid
     * @return MembershipSubscription
     */
    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getPaidAt(): ?DateTime
    {
        return $this->paidAt;
    }

    /**
     * @param DateTimeInterface $paidAt
     * @return MembershipSubscription
     */
    public function setPaidAt(DateTimeInterface $paidAt): self
    {
        $this->paidAt = $paidAt;
        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getTotalSVBinaire(): ?float
    {
        return $this->totalSVBinaire;
    }

    public function setTotalSVBinaire(?float $totalSVBinaire): self
    {
        $this->totalSVBinaire = $totalSVBinaire;
        return $this;
    }

    public function setUpgradable(bool $state)
    {
        $this->upgradable = $state;
    }

    public function isUpgradable()
    {
        return $this->upgradable;
    }

    public function getMembershipUp()
    {
        return $this->membershipUp;
    }

    public function setMembershipUp(array $membershipUp)
    {
        $this->membershipUp = $membershipUp;
    }
}
