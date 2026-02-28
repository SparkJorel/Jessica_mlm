<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;
use App\Validator\Constraints as JTWCAssert;

#[ORM\Entity(repositoryClass: \App\Repository\PromoBonusSpecialRepository::class)]
#[JTWCAssert\StartedAtLessThanEndedAt]
#[JTWCAssert\EligibleGrade]
class PromoBonusSpecial implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: BonusSpecial::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $bonusSpecial;

    #[ORM\Column(type: 'datetime')]
    #[Assert\DateTime]
    #[JTWCAssert\ConformStartedAtDatePromotion]
    private $startedAt;

    #[ORM\Column(type: 'datetime')]
    #[Assert\DateTime]
    #[JTWCAssert\ConformEndedAtDatePromotion]
    private $endedAt;

    #[ORM\Column(type: 'boolean')]
    private $status;

    #[ORM\Column(type: 'boolean')]
    private $underCondition;

    #[ORM\ManyToOne(targetEntity: Grade::class)]
    private $eligibleGrade;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBonusSpecial(): ?BonusSpecial
    {
        return $this->bonusSpecial;
    }

    public function setBonusSpecial(?BonusSpecial $bonusSpecial): self
    {
        $this->bonusSpecial = $bonusSpecial;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(\DateTimeInterface $endedAt): self
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

    public function __toString()
    {
        return $this->getBonusSpecial()->getName();
    }

    public function toString(): string
    {
        return "";
    }

    public function isNew(): bool
    {
        return is_null($this->id);
    }

    public function getUnderCondition(): ?bool
    {
        return $this->underCondition;
    }

    public function setUnderCondition(bool $underCondition): self
    {
        $this->underCondition = $underCondition;

        return $this;
    }

    public function getEligibleGrade(): ?Grade
    {
        return $this->eligibleGrade;
    }

    public function setEligibleGrade(?Grade $eligibleGrade): self
    {
        $this->eligibleGrade = $eligibleGrade;

        return $this;
    }
}
