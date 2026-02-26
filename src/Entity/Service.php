<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use App\AbstractModel\EntityWithImageToUploadInterface;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ServiceRepository")
 */
class Service implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"registration_service"})
     */
    private $type;

    /**
     * @ORM\Column(type="string", length=189, unique=true)
     * @Assert\NotBlank(groups={"registration_service"})
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $slug;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $recordedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\OneToMany(targetEntity="PrestationService", mappedBy="service", cascade={"persist"})
     */
    private $prestationServices;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\AnalyseFonctionnelleSystematique", inversedBy="services")
     */
    private $analyseFonctionnelleSystematiques;

    public function __construct()
    {
        $this->prestationServices = new ArrayCollection();
        $this->analyseFonctionnelleSystematiques = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function toString(): string
    {
        return transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $this->getCode());
    }

    public function isNew(): bool
    {
        return is_null($this->id);
    }

    public function computeSlug()
    {
        if (!$this->slug || '-' === $this->slug) {
            $this->slug = $this->toString();
        }
    }


    /**
     * @return Collection|PrestationService[]
     */
    public function getPrestationServices(): Collection
    {
        return $this->prestationServices;
    }

    public function addPrestationService(PrestationService $prestationService): self
    {
        if (!$this->prestationServices->contains($prestationService)) {
            $this->prestationServices[] = $prestationService;
            $prestationService->setService($this);
        }

        return $this;
    }

    public function removePrestationService(PrestationService $prestationService): self
    {
        if ($this->prestationServices->contains($prestationService)) {
            $this->prestationServices->removeElement($prestationService);
            // set the owning side to null (unless already changed)
            if ($prestationService->getService() === $this) {
                $prestationService->setService(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|AnalyseFonctionnelleSystematique[]
     */
    public function getAnalyseFonctionnelleSystematiques(): Collection
    {
        return $this->analyseFonctionnelleSystematiques;
    }

    public function addAnalyseFonctionnelleSystematique(AnalyseFonctionnelleSystematique $analyseFonctionnelleSystematique): self
    {
        if (!$this->analyseFonctionnelleSystematiques->contains($analyseFonctionnelleSystematique)) {
            $this->analyseFonctionnelleSystematiques[] = $analyseFonctionnelleSystematique;
        }

        return $this;
    }

    public function removeAnalyseFonctionnelleSystematique(AnalyseFonctionnelleSystematique $analyseFonctionnelleSystematique): self
    {
        if ($this->analyseFonctionnelleSystematiques->contains($analyseFonctionnelleSystematique)) {
            $this->analyseFonctionnelleSystematiques->removeElement($analyseFonctionnelleSystematique);
        }

        return $this;
    }
}
