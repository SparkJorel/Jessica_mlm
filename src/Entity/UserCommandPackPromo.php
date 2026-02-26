<?php

namespace App\Entity;

use DateTime;
use Exception;
use DateTimeZone;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use App\AbstractModel\EntityInterface;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserCommandPackPromoRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class UserCommandPackPromo implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $member;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\PackPromo")
     * @ORM\JoinColumn(nullable=false)
     */
    private $pack;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCommand;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateUpdateCommand;

    /**
     * @var boolean
     * @ORM\Column(type="boolean")
     */
    private $delivered;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $code;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMember(): ?User
    {
        return $this->member;
    }

    public function setMember(?User $member): self
    {
        $this->member = $member;
        return $this;
    }

    public function getPack(): ?PackPromo
    {
        return $this->pack;
    }

    public function setPack(?PackPromo $pack): self
    {
        $this->pack = $pack;
        return $this;
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

    public function getDateCommand(): ?DateTimeInterface
    {
        return $this->dateCommand;
    }

    public function setDateCommand(DateTimeInterface $dateCommand): self
    {
        $this->dateCommand = $dateCommand;

        return $this;
    }

    public function getDateUpdateCommand(): ?DateTimeInterface
    {
        return $this->dateUpdateCommand;
    }

    /**
     * @param DateTimeInterface|null $dateUpdateCommand
     * @return UserCommandPackPromo
     */
    public function setDateUpdateCommand(?DateTimeInterface $dateUpdateCommand): self
    {
        $this->dateUpdateCommand = $dateUpdateCommand;
        return $this;
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
     * @return UserCommandPackPromo
     */
    public function setDelivered(bool $delivered): self
    {
        $this->delivered = $delivered;
        return $this;
    }

    /**
     * @ORM\PreUpdate()
     * @throws Exception
     */
    public function preUpdate()
    {
        $this->dateUpdateCommand = new DateTime(
            "now",
            new DateTimeZone("Africa/Douala")
        );
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

    public function toString(): string
    {
        return "";
    }

    public function isNew(): bool
    {
        return is_null($this->id);
    }
}
