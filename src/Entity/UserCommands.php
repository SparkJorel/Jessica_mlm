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
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserCommandsRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class UserCommands implements EntityInterface
{
    /**
     * An order that is in progress, not placed yet.
     *
     * @var string
     */
    public const STATUS_CART = 'cart';

    /**
     * An order that is already placed.
     *
     * @var string
     */
    public const STATUS_ORDERED = 'ordered';


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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $codeParrain;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $motif;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $status = self::STATUS_CART;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCommand;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateCommandUpdate;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $delivered;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $paid;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true, options={"default"=true})
     */
    private $distributor = true;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalDistributorPrice;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalClientPrice;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalSVAP;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $totalSVBinaire;


    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\CommandProducts", mappedBy="command", cascade={"persist", "remove"}, orphanRemoval=true)
     * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
     * @Assert\Valid()
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
        $this->delivered = false;
        $this->paid = false;
    }

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

    public function getCodeParrain(): ?string
    {
        return $this->codeParrain;
    }

    public function setCodeParrain(?string $codeParrain): self
    {
        $this->codeParrain = $codeParrain;
        return $this;
    }

    public function getMotif(): ?string
    {
        return $this->motif;
    }

    public function setMotif(string $motif): self
    {
        $this->motif = $motif;
        return $this;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function getDateCommand(): ?DateTimeInterface
    {
        return $this->dateCommand;
    }

    public function setDateCommand(DateTimeInterface $dateCommand): self
    {
        $this->dateCommand = $dateCommand;
        return $this;
    }

    public function getDateCommandUpdate(): ?DateTimeInterface
    {
        return $this->dateCommandUpdate;
    }

    public function setDateCommandUpdate($dateCommandUpdate): ?self
    {
        $this->dateCommandUpdate = $dateCommandUpdate;
        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection|CommandProducts[]
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(CommandProducts $product): self
    {
        foreach ($this->getProducts() as $existingProduct) {
            // The item already exists, update the quantity
            if ($existingProduct->equals($product)) {
                $existingProduct->setQuantity(
                    $existingProduct->getQuantity() + $product->getQuantity()
                );

                $this->setTotalSVAP($this->processSVAPTotal());
                $this->setTotalSVBinaire($this->processSVBinaireTotal());
                
                if ($this->isDistributor()) {
                    $this->setTotalDistributorPrice($this->getTotalDistributorWithoutShippingCost());
                } else {
                    $this->setTotalClientPrice($this->getTotalClientWithoutShippingCost());
                }

                return $this;
            }
        }

        if ($this->isDistributor()) {
            $product->setDistributor(true);
        } else {
            $product->setDistributor(false);
        }

        $this->products[] = $product;
        $product->setCommand($this);

        return $this;
    }

    public function updateProductQuantity(CommandProducts $product): self
    {
        foreach ($this->getProducts() as $existingProduct) {
            // The item already exists, update the quantity
            if ($existingProduct->equals($product)) {
                $existingProduct->setQuantity(
                    $product->getQuantity()
                );

                $this->setTotalSVAP($this->processSVAPTotal());
                $this->setTotalSVBinaire($this->processSVBinaireTotal());
                
                if ($this->isDistributor()) {
                    $this->setTotalDistributorPrice($this->getTotalDistributorWithoutShippingCost());
                } else {
                    $this->setTotalClientPrice($this->getTotalClientWithoutShippingCost());
                }

                return $this;
            }
        }

        return $this;
    }

    public function removeProduct(CommandProducts $product): self
    {
        if ($this->products->contains($product)) {
            $this->products->removeElement($product);


            $this->setTotalSVAP($this->processSVAPTotal());
            $this->setTotalSVBinaire($this->processSVBinaireTotal());
            
            if ($this->isDistributor()) {
                $this->setTotalDistributorPrice($this->getTotalDistributorWithoutShippingCost());
            } else {
                $this->setTotalClientPrice($this->getTotalClientWithoutShippingCost());
            }

        }

        return $this;
    }

    public function removeProducts()
    {
        if (!$this->products->isEmpty()) {

            $this->products->clear();

            $this->setTotalSVAP($this->processSVAPTotal());
            $this->setTotalSVBinaire($this->processSVBinaireTotal());
            
            if ($this->isDistributor()) {
                $this->setTotalDistributorPrice($this->getTotalDistributorWithoutShippingCost());
            } else {
                $this->setTotalClientPrice($this->getTotalClientWithoutShippingCost());
            }
        }
    }

    public function toString(): string
    {
        // TODO: Implement toString() method.
        return "";
    }

    public function isNew(): bool
    {
        // TODO: Implement isNew() method.
        return is_null($this->id);
    }

    /**
     * @ORM\PreUpdate()
     * @throws Exception
     */
    public function defineDateCommandUpdate(): void
    {
        $dateCommandUpdate = new DateTime("now", new DateTimeZone("Africa/Douala"));
        $this->setDateCommandUpdate($dateCommandUpdate);
	  
	  	$this->setTotalSVAP($this->processSVAPTotal());
        $this->setTotalSVBinaire($this->processSVBinaireTotal());
        
        if ($this->isDistributor()) {
            $this->setTotalDistributorPrice($this->getTotalDistributorWithoutShippingCost());
        } else {
            $this->setTotalClientPrice($this->getTotalClientWithoutShippingCost());
        }

    }

    /**
     * @return bool
     */
    public function isDelivered(): bool
    {
        return $this->delivered;
    }

    /**
     * @param bool $delivered
     * @return UserCommands
     */
    public function setDelivered(bool $delivered): self
    {
        $this->delivered = $delivered;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPaid(): bool
    {
        return $this->paid;
    }

    /**
     * @param bool $paid
     * @return UserCommands
     */
    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;
        return $this;
    }

    /**
     * @param bool $distributor
     * @return UserCommands
     */
    public function setDistributor(bool $distributor): self
    {
        $this->distributor = $distributor;
        return $this;
    }

    /**
     * @return bool
     */
    public function isDistributor(): bool
    {
        return $this->distributor;
    }

    /**
     * @return float|null
     */
    public function getTotalDistributorPrice(): ?float
    {
        return $this->totalDistributorPrice;
    }

    /**
     * @param float|null $totalDistributorPrice
     * @return UserCommands
     */
    public function setTotalDistributorPrice(?float $totalDistributorPrice): self
    {
        $this->totalDistributorPrice = $totalDistributorPrice;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalClientPrice(): ?float
    {
        return $this->totalClientPrice;
    }

    /**
     * @param float|null $totalClientPrice
     * @return UserCommands
     */
    public function setTotalClientPrice(?float $totalClientPrice): self
    {
        $this->totalClientPrice = $totalClientPrice;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalSVAP(): ?float
    {
        return $this->totalSVAP;
    }

    /**
     * @param float|null $totalSVAP
     * @return UserCommands
     */
    public function setTotalSVAP(?float $totalSVAP): self
    {
        $this->totalSVAP = $totalSVAP;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getTotalSVBinaire(): ?float
    {
        return $this->totalSVBinaire;
    }

    /**
     * @param float|null $totalSVBinaire
     * @return UserCommands
     */
    public function setTotalSVBinaire(?float $totalSVBinaire): self
    {
        $this->totalSVBinaire = $totalSVBinaire;
        return $this;
    }

    /**
     * Calculates the order total based of distributor price without shipping.
     *
     * @return float|null
     */
    public function getTotalDistributorWithoutShippingCost(): ?float
    {
        $total = 0;
        /** @var CommandProducts $product */
        foreach ($this->getProducts() as $product) {
            $total += $product->getTotalDistributorPrice();
        }

        return $total;
    }

    /**
     * Calculates the order total based of client price without shipping.
     *
     * @return float|null
     */
    public function getTotalClientWithoutShippingCost(): ?float
    {
        $total = 0;

        /** @var CommandProducts $product */
        foreach ($this->getProducts() as $product) {
            $total += $product->getTotalClientPrice();
        }

        return $total;
    }

    /**
     * Calculates total SV Binaire of the order.
     *
     * @return float|null
     */
    public function processSVBinaireTotal(): ?float
    {
        $total = 0;
        /** @var CommandProducts $product */
        foreach ($this->getProducts() as $product) {
            $total += $product->getTotalItemSVBinaire();
        }

        return $total;
    }

    /**
     * Calculates total SV AP of the order.
     *
     * @return float|null
     */
    public function processSVAPTotal(): ?float
    {
        $total = 0;

        /** @var CommandProducts $product */
        foreach ($this->getProducts() as $product) {
            $total += $product->getTotalItemSVAP();
        }

        return $total;
    }
}
