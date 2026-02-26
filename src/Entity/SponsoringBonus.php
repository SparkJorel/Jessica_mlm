<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * Class GetBonus
 * @package App\Entity
 * @ORM\Entity(repositoryClass="App\Repository\SponsoringBonusRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Cache(usage="NONSTRICT_READ_WRITE")
 */
class SponsoringBonus implements EntityInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var bool
     * @ORM\Column(type="boolean")
     */
    private $paid;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime")
     */
    private $dateActivation;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateBonusPaid;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $membership;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $sponsorised;

    /**
     * @var float
     * @ORM\Column(type="float", nullable=true)
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=false)
     */
    private $sponsor;

    /**
     * @return mixed
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @ORM\PrePersist()
     * @throws Exception
     */
    public function autoSave(): void
    {
        $this->paid = false;
    }

    /**
     * @ORM\PreUpdate()
     * @throws Exception
     */
    public function onPreUpdate(): void
    {
        $this->dateBonusPaid = new DateTime("now", new DateTimeZone("Africa/Douala"));
    }

    /**
     * @return User
     */
    public function getSponsor()
    {
        return $this->sponsor;
    }

    /**
     * @param mixed $sponsor
     * @return SponsoringBonus
     */
    public function setSponsor(User $sponsor): self
    {
        $this->sponsor = $sponsor;
        return $this;
    }

    /**
     * @return User
     */
    public function getSponsorised()
    {
        return $this->sponsorised;
    }

    /**
     * @param string $sponsorised
     * @return SponsoringBonus
     */
    public function setSponsorised($sponsorised): self
    {
        $this->sponsorised = $sponsorised;

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
     * @return SponsoringBonus
     */
    public function setPaid(bool $paid): self
    {
        $this->paid = $paid;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateBonusPaid(): DateTime
    {
        return $this->dateBonusPaid;
    }

    /**
     * @param DateTime $dateBonusPaid
     * @return SponsoringBonus
     */
    public function setDateBonusPaid(DateTime $dateBonusPaid): self
    {
        $this->dateBonusPaid = $dateBonusPaid;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDateActivation(): DateTime
    {
        return $this->dateActivation;
    }

    /**
     * @param DateTime $dateActivation
     * @return SponsoringBonus
     */
    public function setDateActivation(DateTime $dateActivation): self
    {
        $this->dateActivation = $dateActivation;
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

    /**
     * @return string
     */
    public function getMembership(): ?string
    {
        return $this->membership;
    }

    /**
     * @param string $membership
     * @return SponsoringBonus
     */
    public function setMembership($membership): self
    {
        $this->membership = $membership;
        return $this;
    }

    /**
     * @return float
     */
    public function getValue(): ?float
    {
        return $this->value;
    }

    /**
     * @param float $value
     * @return SponsoringBonus
     */
    public function setValue(float $value): self
    {
        $this->value = $value;
        return $this;
    }
}
