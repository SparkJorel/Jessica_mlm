<?php

namespace App\Entity;

use App\AbstractModel\EntityInterface;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Repository\LevelBonusGenerationnelRepository::class)]
#[ORM\Cache(usage: 'READ_ONLY')]
class LevelBonusGenerationnel implements EntityInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $lvl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLvl(): ?int
    {
        return $this->lvl;
    }

    public function setLvl(int $lvl): self
    {
        $this->lvl = $lvl;
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
