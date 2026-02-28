<?php

namespace App\Entity;

use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: \App\Repository\UserBonusSpecialRepository::class)]
#[ORM\HasLifecycleCallbacks]
class UserBonusSpecial
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $user;

    #[ORM\ManyToOne(targetEntity: BonusSpecial::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $bonus;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $month;

    #[ORM\Column(type: 'datetime')]
    private $recordedAt;

    #[ORM\Column(type: 'datetime')]
    private $startedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $endedAt;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private $status;

    #[ORM\Column(type: 'boolean')]
    private $promo;

    #[ORM\Column(type: 'boolean', options: ['default' => true])]
    private $firstCondition;

    #[ORM\Column(type: 'boolean', options: ['default' => false])]
    private $secondCondition;

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

    public function getBonus(): ?BonusSpecial
    {
        return $this->bonus;
    }

    public function setBonus(?BonusSpecial $bonus): self
    {
        $this->bonus = $bonus;

        return $this;
    }

    public function getMonth(): ?string
    {
        return $this->month;
    }

    public function setMonth(string $month): self
    {
        $this->month = $month;

        return $this;
    }

    public function getRecordedAt(): ?DateTimeInterface
    {
        return $this->recordedAt;
    }

    public function setRecordedAt(DateTimeInterface $recordedAt): self
    {
        $this->recordedAt = $recordedAt;

        return $this;
    }

    public function getStartedAt(): ?DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getEndedAt(): ?DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(?DateTimeInterface $endedAt): self
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    public function setStatus(bool $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getPromo(): ?bool
    {
        return $this->promo;
    }

    public function setPromo(bool $promo): self
    {
        $this->promo = $promo;
        return $this;
    }

    /**
     * @throws Exception
     */
    #[ORM\PrePersist]
    public function onPersist()
    {
        $this->recordedAt = new DateTime("now", new DateTimeZone("Africa/Douala"));
    }

    public function getFirstCondition(): ?bool
    {
        return $this->firstCondition;
    }

    public function setFirstCondition(bool $firstCondition): self
    {
        $this->firstCondition = $firstCondition;

        return $this;
    }

    public function getSecondCondition(): ?bool
    {
        return $this->secondCondition;
    }

    public function setSecondCondition(bool $secondCondition): self
    {
        $this->secondCondition = $secondCondition;

        return $this;
    }
}
