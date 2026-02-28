<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: \App\Repository\TVCPackRepository::class)]
#[ORM\HasLifecycleCallbacks]
class TVCPack implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\ManyToOne(targetEntity: Membership::class)]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull(groups: ['registration_tvc'])]
    private $membership;

    #[ORM\Column(type: 'float')]
    #[Assert\NotBlank(groups: ['registration_tvc'])]
    private $value;

    #[ORM\Column(type: 'datetime')]
    private $startedAt;

    #[ORM\Column(type: 'boolean')]
    private $status;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $endedAt;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getStartedAt(): ?\DateTimeInterface
    {
        return $this->startedAt;
    }

    public function setStartedAt(\DateTimeInterface $startedAt): self
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
    /**
     * @throws Exception
     */
    #[ORM\PrePersist]
    public function prePersist()
    {
        $this->status = true;
        $this->startedAt = new DateTime("now", new DateTimeZone("Africa/Douala"));
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
