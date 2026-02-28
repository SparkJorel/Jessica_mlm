<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use DateTime;
use DateTimeInterface;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\MembershipSVRepository::class)]
#[ORM\HasLifecycleCallbacks]
class MembershipSV implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'float')]
    #[Assert\Positive(groups: ['registration'])]
    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\Type(type: 'float', message: 'La valeur {{ value }} n\'est pas un flottant valide', groups: ['registration'])]
    private $svGroupe;

    #[ORM\Column(type: 'float')]
    #[Assert\Positive(groups: ['registration'])]
    #[Assert\NotBlank(groups: ['registration'])]
    #[Assert\Type(type: 'float', message: 'La valeur {{ value }} n\'est pas un flottant valide', groups: ['registration'])]
    private $svProduct;

    #[ORM\Column(type: 'boolean')]
    private $state;

    #[ORM\Column(type: 'datetime')]
    private $started;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $endedAt;

    #[ORM\ManyToOne(targetEntity: Membership::class)]
    private $membership;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSvGroupe(): ?float
    {
        return $this->svGroupe;
    }

    public function setSvGroupe(float $svGroupe): self
    {
        $this->svGroupe = $svGroupe;
        return $this;
    }

    public function getSvProduct(): ?float
    {
        return $this->svProduct;
    }

    public function setSvProduct(float $svProduct): self
    {
        $this->svProduct = $svProduct;
        return $this;
    }

    public function getState(): ?bool
    {
        return $this->state;
    }

    /**
     * @param bool $state
     * @return MembershipSV
     */
    public function setState(bool $state): self
    {
        $this->state = $state;
        return $this;
    }

    public function getStarted(): ?DateTimeInterface
    {
        return $this->started;
    }

    /**
     * @param DateTimeInterface $started
     * @return MembershipSV
     */
    public function setStarted(DateTimeInterface $started): self
    {
        $this->started = $started;
        return $this;
    }

    public function getEndedAt(): ?DateTimeInterface
    {
        return $this->endedAt;
    }

    /**
     * @throws Exception
     */
    #[ORM\PrePersist]
    public function prePersist(): void
    {
        $this->state = true;
        $this->started = new DateTime("now", new DateTimeZone("Africa/Douala"));
    }

    /**
     * @throws Exception
     */
    #[ORM\PreUpdate]
    public function postUpdate()
    {
        $this->endedAt = new DateTime("now", new DateTimeZone("Africa/Douala"));
    }

    public function toString(): string
    {
        return "";
    }

    public function isNew(): bool
    {
        return is_null($this->id);
    }

    public function getMembership(): ?Membership
    {
        return $this->membership;
    }

    public function setMembership(?Membership $membership): self
    {
        $this->membership = $membership;

        return $this;
    }
}
