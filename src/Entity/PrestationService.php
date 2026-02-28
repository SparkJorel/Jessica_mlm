<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use App\AbstractModel\EntityWithImageToUploadInterface;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\PrestationServiceRepository::class)]
#[ORM\HasLifecycleCallbacks]
class PrestationService implements EntityInterface, EntityWithImageToUploadInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 190, unique: true)]
    private $name;

    #[ORM\Column(type: 'string', length: 190, unique: true)]
    private $code;

    #[ORM\Column(type: 'string', length: 255)]
    private $slug;

    #[ORM\Column(type: 'integer')]
    private $cost;

    #[ORM\Column(type: 'float')]
    private $pourcentagePrescripteurSNLMembre;

    #[ORM\Column(type: 'float')]
    private $pourcentagePrescripteurSNL;

    #[ORM\Column(type: 'float')]
    private $pourcentageSponsorPrescripteurSNL;

    #[ORM\Column(type: 'float')]
    private $binaire;

    #[ORM\Column(type: 'boolean')]
    private $status;

    #[ORM\Column(type: 'datetime')]
    private $recordedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $startedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $endedAt;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $updatedAt;

    #[ORM\ManyToOne(targetEntity: Service::class, inversedBy: 'prestationServices')]
    #[ORM\JoinColumn(nullable: false)]
    private $service;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $brochureServiceFilename;

    #[Assert\File(maxSize: '1024k', mimeTypes: ['image/jpeg', 'image/png'])]
    private $file;

    #[ORM\ManyToMany(targetEntity: AnalyseFonctionnelleSystematique::class)]
    private $analyseFonctionnelleSystematiques;

    #[ORM\Column(type: 'string', length: 20, nullable: true)]
    private $duree;

    public function __construct()
    {
        $this->analyseFonctionnelleSystematiques = new ArrayCollection();
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

    public function getCost(): ?int
    {
        return $this->cost;
    }

    public function setCost(int $cost): self
    {
        $this->cost = $cost;

        return $this;
    }

    public function getPourcentagePrescripteurSNLMembre(): ?float
    {
        return $this->pourcentagePrescripteurSNLMembre;
    }

    public function setPourcentagePrescripteurSNLMembre(float $pourcentagePrescripteurSNLMembre): self
    {
        $this->pourcentagePrescripteurSNLMembre = $pourcentagePrescripteurSNLMembre;

        return $this;
    }

    public function getPourcentagePrescripteurSNL(): ?float
    {
        return $this->pourcentagePrescripteurSNL;
    }

    public function setPourcentagePrescripteurSNL(float $pourcentagePrescripteurSNL): self
    {
        $this->pourcentagePrescripteurSNL = $pourcentagePrescripteurSNL;

        return $this;
    }

    public function getPourcentageSponsorPrescripteurSNL(): ?float
    {
        return $this->pourcentageSponsorPrescripteurSNL;
    }

    public function setPourcentageSponsorPrescripteurSNL(float $pourcentageSponsorPrescripteurSNL): self
    {
        $this->pourcentageSponsorPrescripteurSNL = $pourcentageSponsorPrescripteurSNL;

        return $this;
    }

    public function getBinaire(): ?float
    {
        return $this->binaire;
    }

    public function setBinaire(float $binaire): self
    {
        $this->binaire = $binaire;

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

    public function getRecordedAt(): ?DateTimeInterface
    {
        return $this->recordedAt;
    }

    public function setRecordedAt(DateTimeInterface $recordedAt): self
    {
        $this->recordedAt = $recordedAt;

        return $this;
    }
    public function getEndedAt(): ?DateTimeInterface
    {
        return $this->endedAt;
    }

    /**
     * @param DateTimeInterface $endedAt
     * @return PrestationService
     */
    public function setEndedAt(?DateTimeInterface $endedAt): self
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
     * @return PrestationService
     */
    public function setStartedAt(?DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;
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

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): self
    {
        $this->service = $service;

        return $this;
    }

    public function getBrochureServiceFilename(): ?string
    {
        return $this->brochureServiceFilename;
    }

    public function setBrochureServiceFilename(?string $brochureServiceFilename): self
    {
        $this->brochureServiceFilename = $brochureServiceFilename;

        return $this;
    }

    /**
     * Sets file.
     *
     * @param UploadedFile $file
     */
    public function setFile(UploadedFile $file = null)
    {
        $this->file = $file;
    }

    /**
     * Get file.
     *
     * @param null $type
     * @return UploadedFile|null
     */
    public function getFile($type = null): ?UploadedFile
    {
        return $this->file;
    }


    /**
     * @throws Exception
     */
    #[ORM\PrePersist]
    public function prePersistValue(): void
    {
        $this->recordedAt = new DateTime("now", new DateTimeZone("Africa/Douala"));
    }

    /**
     * @throws Exception
     */
    #[ORM\PreUpdate]
    public function preUpdateValue(): void
    {
        $this->updatedAt = new DateTime("now", new DateTimeZone("Africa/Douala"));
    }

    public function isNew(): bool
    {
        return is_null($this->id);
    }

    public function toString(): string
    {
        return transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $this->getName());
    }

    public function computeSlug()
    {
        if (!$this->slug || '-' === $this->slug) {
            $this->slug = $this->toString();
        }
    }

    public function getSpecificDirectory(string $baseDirectory, string $type = null): string
    {
        $localDirectory = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $this->getService()->getCode());
        if (!is_dir($baseDirectory."/".$localDirectory)) {
            mkdir($baseDirectory."/".$localDirectory, 0644);
        }

        return $baseDirectory."/".$localDirectory;
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

    public function getDuree(): ?string
    {
        return $this->duree;
    }

    public function setDuree(?string $duree): self
    {
        $this->duree = $duree;

        return $this;
    }
}
