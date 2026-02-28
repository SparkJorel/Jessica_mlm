<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\GradeRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @UniqueEntity(fields={"commercialName", "weight", "maintenance", "lvl", "sv"}, ignoreNull=true, groups={"registration_grade"})
 */
class Grade implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"registration_grade"})
     */
    private $commercialName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"registration_grade"})
     */
    private $technicalName;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="float", nullable=true)
     *
     * @Assert\NotBlank(groups={"registration_grade"})
     * @Assert\Positive(groups={"registration_grade"})
     */
    private $maintenance;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Assert\NotBlank(groups={"registration_grade"})
     * @Assert\Positive(groups={"registration_grade"})
     */
    private $lvl;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Assert\NotBlank(groups={"registration_grade"})
     * @Assert\Positive(groups={"registration_grade"})
     */
    private $sv;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\GradeBG", mappedBy="grade")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $gradeBGs;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default": false})
     */
    private $rewardable;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $weight;

    public function __construct()
    {
        $this->gradeBGs = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCommercialName(): ?string
    {
        return $this->commercialName;
    }

    public function setCommercialName(string $commercialName): self
    {
        $this->commercialName = $commercialName;

        return $this;
    }

    public function getTechnicalName(): ?string
    {
        return $this->technicalName;
    }

    public function setTechnicalName(string $technicalName): self
    {
        $this->technicalName = $technicalName;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getMaintenance(): ?float
    {
        return $this->maintenance;
    }

    public function setMaintenance(?float $maintenance): self
    {
        $this->maintenance = $maintenance;

        return $this;
    }

    public function getSv(): ?float
    {
        return $this->sv;
    }

    public function setSv(?float $sv): self
    {
        $this->sv = $sv;

        return $this;
    }


    public function getLvl(): ?int
    {
        return $this->lvl;
    }

    public function setLvl(?int $lvl): self
    {
        $this->lvl = $lvl;

        return $this;
    }

    /**
     * @return Collection|GradeBG[]
     */
    public function getGradeBGs(): Collection
    {
        return $this->gradeBGs;
    }

    public function addGradeBG(GradeBG $gradeBG): self
    {
        if (!$this->gradeBGs->contains($gradeBG)) {
            $this->gradeBGs[] = $gradeBG;
            $gradeBG->setGrade($this);
        }

        return $this;
    }

    public function removeGradeBG(GradeBG $gradeBG): self
    {
        if ($this->gradeBGs->contains($gradeBG)) {
            $this->gradeBGs->removeElement($gradeBG);
            // set the owning side to null (unless already changed)
            if ($gradeBG->getGrade() === $this) {
                $gradeBG->setGrade(null);
            }
        }

        return $this;
    }

    public function __toString(): string
    {
        return $this->getCommercialName();
    }

    public function toString(): string
    {
        return $this->getCommercialName();
        
    }

    public function isNew(): bool
    {
        return is_null($this->id);
    }

    public function isRewardable(): ?bool
    {
        return $this->rewardable;
    }

    public function setRewardable(?bool $rewardable): self
    {
        $this->rewardable = $rewardable;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(?int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }
}
