<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CycleRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 * @ORM\HasLifecycleCallbacks()
 */
class Cycle implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(groups={"registration_cycle"})
     */
    private $startedAt;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(groups={"registration_cycle"})
     */
    private $endedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $active;


    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default"=false})
     */
    private $weekly;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default"=false})
     */
    private $autoSave;

    /**
     * @ORM\Column(type="boolean", nullable=true, options={"default"=false})
     */
    private $binarySaved;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $closed;
  

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getStartedAt(): ?DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

        return $this;
    }

    /**
     * @return DateTimeInterface|null
     */
    public function getEndedAt(): ?DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(DateTimeInterface $endedAt): self
    {
        $this->endedAt = $endedAt;
        return $this;
    }

    public function getActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): self
    {
        $this->active = $active;
        return $this;
    }

    public function getWeekly(): ?bool
    {
        return $this->weekly;
    }

    public function setWeekly(bool $weekly): self
    {
        $this->weekly = $weekly;
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
     * @ORM\PrePersist()
     */
    public function onPersist()
    {
        $this->active = true;
    }

    public function getClosed(): ?bool
    {
        return $this->closed;
    }

    public function setClosed(bool $closed): self
    {
        $this->closed = $closed;

        return $this;
    }

    public function getBinarySaved(): ?bool
    {
        return $this->binarySaved;
    }

    public function setBinarySaved(bool $binarySaved): self
    {
        $this->binarySaved = $binarySaved;

        return $this;
    }

    public function getAutoSave(): ?bool
    {
        return $this->autoSave;
    }

    public function setAutoSave(bool $autoSave): self
    {
        $this->autoSave = $autoSave;
        return $this;
    }
}
