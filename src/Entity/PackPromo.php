<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PackPromoRepository")
 * @UniqueEntity(fields={"code"})
 * @ORM\HasLifecycleCallbacks()
 */
class PackPromo implements EntityInterface
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
    private $code;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Le nom de baptême de la promo doit être renseigné")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $startedAt;

    /**
     * @ORM\Column(type="datetime")
     */
    private $endedAt;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $started;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $ended;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\PromoPackProduct", cascade={"persist"}, mappedBy="promo")
     * @Assert\Valid()
     */
    private $products;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function __construct()
    {
        $this->products = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;
        return $this;
    }

    public function getStartedAt(): ?DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(DateTimeInterface $startedAt): self
    {
        $this->startedAt = $startedAt;
        return $this;
    }

    public function getEndedAt(): ?DateTimeInterface
    {
        return $this->endedAt;
    }

    public function setEndedAt(DateTimeInterface $endedAt): self
    {
        $this->endedAt = $endedAt;
        return $this;
    }

    /**
     * @return bool
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * @param bool $started
     * @return PackPromo
     */
    public function setStarted(bool $started): self
    {
        $this->started = $started;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEnded(): bool
    {
        return $this->ended;
    }

    /**
     * @throws Exception
     * @ORM\PrePersist()
     */
    public function prePersist()
    {
        $now = new DateTime("now", new DateTimeZone("Africa/Douala"));
        if ($now >= $this->getStartedAt()) {
            $this->setStarted(true);
        } else {
            $this->setStarted(false);
        }

        if ($now <= $this->getEndedAt()) {
            $this->setEnded(false);
        } else {
            $this->setEnded(true);
        }
    }

    /**
     * @param bool $ended
     * @return PackPromo
     */
    public function setEnded(bool $ended): self
    {
        $this->ended = $ended;
        return $this;
    }

    /**
     * @return Collection|PromoPackProduct[]
     */
    public function getProducts()
    {
        return $this->products;
    }

    public function addProduct(PromoPackProduct $product)
    {
        if (!$this->products->contains($product)) {
            $product->setPromo($this);
            $this->products->add($product);
        }

        return $this;
    }

    public function removeProduct(PromoPackProduct $product)
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);
        }
        return $this;
    }

    public function toString(): string
    {
        return '';
    }

    public function isNew(): bool
    {
        return is_null($this->id);
    }
}
