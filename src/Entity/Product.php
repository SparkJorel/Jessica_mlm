<?php

namespace App\Entity;

use DateTime;
use Exception;
use DateTimeZone;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use App\AbstractModel\EntityInterface;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductRepository")
 * @UniqueEntity(fields={"code"}, groups={"registration_product"}, message="Cette valeur est dejà présente dans la plateforme")
 * @Vich\Uploadable
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class Product implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"registration_product"})
     */
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"registration_product"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(groups={"registration_product"})
     */
    private $clientPrice;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(groups={"registration_product"})
     */
    private $productCote;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(groups={"registration_product"})
     */
    private $distributorPrice;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(groups={"registration_product"})
     */
    private $productSV;

    /**
     * @ORM\Column(type="float")
     * @Assert\NotBlank(groups={"registration_product"})
     */
    private $productSVBPA;

    /**
     * NOTE: This is not a mapped field of entity metadata, just a simple property.
     *
     * @Vich\UploadableField(mapping="products", fileNameProperty="imageName")
     *
     * @var File
     */
    private $imageFile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $imageName;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var DateTime
     */
    private $updatedAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;


    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $recordedAt;


    public function getId(): ?int
    {
        return $this->id;
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

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return Product
     */
    public function setDescription($description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     * @return Product
     */
    public function setStatus(bool $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * @param DateTimeInterface $updatedAt
     * @return Product
     */
    public function setUpdatedAt(?DateTimeInterface $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }


    public function toString(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->code;
    }

    public function isNew(): bool
    {
        return is_null($this->id);
    }

    /**
     * If manually uploading a file (i.e. not using Symfony Form) ensure an instance
     * of 'UploadedFile' is injected into this setter to trigger the update. If this
     * bundle's configuration parameter 'inject_on_load' is set to 'true' this setter
     * must be able to accept an instance of 'File' as the bundle will inject one here
     * during Doctrine hydration.
     *
     * @param File|UploadedFile $imageFile
     * @return Product
     * @throws Exception
     */
    public function setImageFile(?File $imageFile = null): self
    {
        $this->imageFile = $imageFile;

        if ($imageFile) {
            $this->updatedAt = new \DateTimeImmutable("now", new DateTimeZone("Africa/Douala"));
        }

        return $this;
    }

    public function getImageFile(): ?File
    {
        return $this->imageFile;
    }

    public function setImageName(?string $imageName): void
    {
        $this->imageName = $imageName;
    }

    public function getImageName(): ?string
    {
        return $this->imageName;
    }

    public function getRecordedAt(): ?\DateTimeInterface
    {
        return $this->recordedAt;
    }

    public function setRecordedAt(?\DateTimeInterface $recordedAt): self
    {
        $this->recordedAt = $recordedAt;

        return $this;
    }

    public function getClientPrice(): ?float
    {
        return $this->clientPrice;
    }

    public function setClientPrice(?float $clientPrice): self
    {
        $this->clientPrice = $clientPrice;

        return $this;
    }

    public function getProductCote(): ?float
    {
        return $this->productCote;
    }

    public function setProductCote(?float $productCote): self
    {
        $this->productCote = $productCote;

        return $this;
    }

    public function getDistributorPrice(): ?float
    {
        return $this->distributorPrice;
    }

    public function setDistributorPrice(?float $distributorPrice): self
    {
        $this->distributorPrice = $distributorPrice;

        return $this;
    }

    public function getProductSV(): ?float
    {
        return $this->productSV;
    }

    public function setProductSV(?float $productSV): self
    {
        $this->productSV = $productSV;

        return $this;
    }

    public function getProductSVBPA(): ?float
    {
        return $this->productSVBPA;
    }

    public function setProductSVBPA(?float $productSVBPA): self
    {
        $this->productSVBPA = $productSVBPA;

        return $this;
    }
}
