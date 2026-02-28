<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;
use DateTimeZone;

#[ORM\Entity(repositoryClass: \App\Repository\GradeBGRepository::class)]
#[UniqueEntity(fields: ['grade', 'name', 'value'])]
#[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
#[ORM\HasLifecycleCallbacks]
class GradeBG implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Grade::class, inversedBy: 'gradeBGs')]
    #[ORM\JoinColumn(nullable: false)]
    #[ORM\Cache(usage: 'NONSTRICT_READ_WRITE')]
    #[Assert\NotBlank(groups: ['registration_grade_bg'])]
    private $grade;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank(groups: ['registration_grade_bg'])]
    private $name;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(groups: ['registration_grade_bg'])]
    #[Assert\Positive(groups: ['registration_grade_bg'])]
    private $value;

    #[ORM\Column(type: 'boolean')]
    private $status;

    #[ORM\Column(type: 'datetime')]
    private $recordedAt;

    #[ORM\Column(type: 'datetime')]
    private $startedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $endedAt;

    #[ORM\ManyToOne(targetEntity: LevelBonusGenerationnel::class)]
    #[ORM\JoinColumn(nullable: false)]
    private $lvl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGrade(): ?Grade
    {
        return $this->grade;
    }

    public function setGrade(?Grade $grade): self
    {
        $this->grade = $grade;

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

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

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

    public function getRecordedAt(): ?\DateTimeInterface
    {
        return $this->recordedAt;
    }

    public function setRecordedAt(\DateTimeInterface $recordedAt): self
    {
        $this->recordedAt = $recordedAt;

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

    public function setEndedAt(?\DateTimeInterface $endedAt): self
    {
        $this->endedAt = $endedAt;

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

    public function getLvl(): ?LevelBonusGenerationnel
    {
        return $this->lvl;
    }

    public function setLvl(LevelBonusGenerationnel $lvl): self
    {
        $this->lvl = $lvl;
        return $this;
    }
  
  	/**
     * @throws Exception
  	 */
  	#[ORM\PrePersist]
    public function gradeBGPrePersist(): void
    {
        $this->status = true;
        $this->recordedAt = new DateTime("now", new DateTimeZone("Africa/Douala"));
    }
}
