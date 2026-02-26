<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProductClientPriceRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class ProductClientPrice implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     */
    private $price;

    /**
     * @ORM\Column(type="boolean")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $applyingDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $expirationDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Product", inversedBy="productCPs")
     * @ORM\JoinColumn(nullable=false)
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $product;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): self
    {
        $this->price = $price;
        return $this;
    }

    public function getStatus(): ?bool
    {
        return $this->status;
    }

    /**
     * @param bool $status
     * @return ProductClientPrice
     */
    public function setStatus(bool $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getApplyingDate(): ?DateTimeInterface
    {
        return $this->applyingDate;
    }

    /**
     * @param DateTimeInterface $applyingDate
     * @return ProductClientPrice
     */
    public function setApplyingDate(DateTimeInterface $applyingDate): self
    {
        $this->applyingDate = $applyingDate;
        return $this;
    }

    public function getExpirationDate(): ?DateTimeInterface
    {
        return $this->expirationDate;
    }

    /**
     * @param DateTimeInterface $expirationDate
     * @return ProductClientPrice
     */
    public function setExpirationDate(DateTimeInterface $expirationDate): self
    {
        $this->expirationDate = $expirationDate;
        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): self
    {
        $this->product = $product;
        return $this;
    }

    public function toString(): string
    {
        return "";
    }

    public function isNew(): bool
    {
        // TODO: Implement isNew() method.
        return is_null($this->id);
    }
}
