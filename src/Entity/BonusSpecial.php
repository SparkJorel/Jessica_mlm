<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use App\AbstractModel\EntityWithImageToUploadInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BonusSpecialRepository")
 * @UniqueEntity(fields={"name", "weight"})
 */
class BonusSpecial implements EntityInterface, EntityWithImageToUploadInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=190, unique=true)
     */
    private $slug;

    /**
     * @ORM\Column(type="integer")
     */
    private $cap1;

    /**
     * @ORM\Column(type="integer")
     */
    private $cap2;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $videoFile;

    /**
     * @var UploadedFile
     */
    private $image;

    /**
     * @var UploadedFile
     */
    private $video;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endedAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Grade")
     */
    private $grade;

    /**
     * @ORM\Column(type="boolean", options={"default": false})
     */
    private $promoActivated;

    /**
     * @ORM\Column(type="integer")
     */
    private $weight;

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

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getCap1(): ?int
    {
        return $this->cap1;
    }

    public function setCap1(int $cap1): self
    {
        $this->cap1 = $cap1;

        return $this;
    }

    public function getCap2(): ?int
    {
        return $this->cap2;
    }

    public function setCap2(int $cap2): self
    {
        $this->cap2 = $cap2;

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

    public function getImageFile(): ?string
    {
        return $this->imageFile;
    }

    public function setImageFile(?string $imageFile): self
    {
        $this->imageFile = $imageFile;

        return $this;
    }

    public function getVideoFile(): ?string
    {
        return $this->videoFile;
    }

    public function setVideoFile(?string $videoFile): self
    {
        $this->videoFile = $videoFile;

        return $this;
    }

    public function getImage(): ?UploadedFile
    {
        return $this->image;
    }

    public function setImage(?UploadedFile $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getVideo(): ?UploadedFile
    {
        return $this->video;
    }

    public function setVideo(?UploadedFile $video): self
    {
        $this->video = $video;
        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(?\DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;

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

    public function getEndedAt(): ?\DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(?\DateTimeInterface $endedAt): self
    {
        $this->endedAt = $endedAt;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?\DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function toString(): string
    {
        return transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $this->getName(). ' ' .$this->getCap1(). ' ' .$this->getCap2());
    }

    public function __toString()
    {
        return $this->getName();
    }

    public function isNew(): bool
    {
        return is_null($this->id);
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

    public function computeSlug()
    {
        if (!$this->slug || '-' === $this->slug) {
            $this->slug = $this->toString();
        }
    }

    public function getSpecificDirectory(string $baseDirectory, $type = 'image'): string
    {
        if ($type === 'image') {
            $directory = $baseDirectory.'/bonus_special/images';
        } else {
            $directory = $baseDirectory.'/bonus_special/videos';
        }

        if (!is_dir($directory)) {
            mkdir($directory, 0644);
        }

        return $directory;
    }

    public function getFile(string $type = null): ?UploadedFile
    {
        if (!$type || $type === 'image') {
            return $this->getImage();
        } else {
            return $this->getVideo();
        }
    }

    public function getPromoActivated(): ?bool
    {
        return $this->promoActivated;
    }

    public function setPromoActivated(bool $promoActivated): self
    {
        $this->promoActivated = $promoActivated;

        return $this;
    }

    public function getWeight(): ?int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): self
    {
        $this->weight = $weight;

        return $this;
    }
}
