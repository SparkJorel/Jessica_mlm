<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CommandProductsRepository")
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class CommandProducts
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Cette valeur doit être renseignée",
     * groups={"add_to_card", "cart_item"})
     * @Assert\Positive(message="La quantité doit être supérieure à 0")
     */
    private $quantity;

    /**
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="App\Entity\Product")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @var UserCommands
     * @Assert\NotBlank()
     * @ORM\ManyToOne(targetEntity="App\Entity\UserCommands", inversedBy="products")
     */
    private $command;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $itemDistributorPrice;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $itemClientPrice;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $itemSVAP;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $itemSVBinaire;

    /**
     * @var boolean
     * @ORM\Column(type="boolean", nullable=true, options={"default"=true})
     */
    private $distributor = true;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

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

    /**
     * @return UserCommands
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param UserCommands $command
     * @return CommandProducts
     */
    public function setCommand(UserCommands $command = null): self
    {
        $this->command = $command;
        return $this;
    }


    /**
     * @return float|null
     */
    public function getItemDistributorPrice(): ?float
    {
        return $this->itemDistributorPrice;
    }

    /**
     * @param float|null $itemDistributorPrice
     * @return CommandProducts
     */
    public function setItemDistributorPrice(?float $itemDistributorPrice): self
    {
        $this->itemDistributorPrice = $itemDistributorPrice;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getItemClientPrice(): ?float
    {
        return $this->itemClientPrice;
    }

    /**
     * @param float|null $itemClientPrice
     * @return CommandProducts
     */
    public function setItemClientPrice(?float $itemClientPrice): self
    {
        $this->itemClientPrice = $itemClientPrice;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getItemSVAP(): ?float
    {
        return $this->itemSVAP;
    }

    /**
     * @param float|null $itemSVAP
     * @return CommandProducts
     */
    public function setItemSVAP(?float $itemSVAP): self
    {
        $this->itemSVAP = $itemSVAP;
        return $this;
    }

    /**
     * @return float|null
     */
    public function getItemSVBinaire(): ?float
    {
        return $this->itemSVBinaire;
    }

    /**
     * @param float|null $itemSVBinaire
     * @return CommandProducts
     */
    public function setItemSVBinaire(?float $itemSVBinaire): self
    {
        $this->itemSVBinaire = $itemSVBinaire;
        return $this;
    }

    /**
     * Tests if the given item given corresponds to the same order item.
     *
     * @param CommandProducts $item
     *
     * @return bool
     */
    public function equals(CommandProducts $item): bool
    {
        return $this->getProduct()->getId() === $item->getProduct()->getId();
    }

    /**
     * Calculates the item distributor total.
     *
     * @return float
     */
    public function getTotalDistributorPrice(): ?float
    {
        return $this->getProduct()->getDistributorPrice() * $this->getQuantity();
    }

    /**
     * Calculates the item client total.
     *
     * @return float
     */
    public function getTotalClientPrice(): ?float
    {
        return $this->getProduct()->getClientPrice() * $this->getQuantity();
    }


    /**
     * Calculates the item distributor total.
     *
     * @return float
     */
    public function getTotalItemSVBinaire(): ?float
    {
        return $this->getProduct()->getProductSV() * $this->getQuantity();
    }

    /**
     * Calculates the item client total.
     *
     * @return float
     */
    public function getTotalItemSVAP(): ?float
    {
        return $this->getProduct()->getProductSVBPA() * $this->getQuantity();
    }

    /**
     * @param bool $distributor
     * @return CommandProducts
     */
    public function setDistributor(?bool $distributor): self
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
}
