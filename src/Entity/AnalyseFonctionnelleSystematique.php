<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\AnalyseFonctionnelleSystematiqueRepository::class)]
class AnalyseFonctionnelleSystematique implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'text', nullable: true)]
    private $description;

    #[ORM\Column(type: 'boolean')]
    private $unit;

    #[ORM\ManyToOne(targetEntity: AnalyseFonctionnelleSystematique::class)]
    private $groupUnit;

    #[ORM\ManyToMany(targetEntity: Service::class, mappedBy: 'analyseFonctionnelleSystematiques')]
    private $services;

    public function __construct()
    {
        $this->services = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getUnit(): ?bool
    {
        return $this->unit;
    }

    public function setUnit(bool $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getGroupUnit(): ?self
    {
        return $this->groupUnit;
    }

    public function setGroupUnit(?self $groupUnit): self
    {
        $this->groupUnit = $groupUnit;

        return $this;
    }

    /**
     * @return Collection|Service[]
     */
    public function getServices(): Collection
    {
        return $this->services;
    }

    public function addService(Service $service): self
    {
        if (!$this->services->contains($service)) {
            $this->services[] = $service;
            $service->addAnalyseFonctionnelleSystematique($this);
        }

        return $this;
    }

    public function removeService(Service $service): self
    {
        if ($this->services->contains($service)) {
            $this->services->removeElement($service);
            $service->removeAnalyseFonctionnelleSystematique($this);
        }

        return $this;
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function isNew(): bool
    {
        return is_null($this->id);
    }
}
