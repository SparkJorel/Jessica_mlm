<?php

declare(strict_types=1);

namespace App\Entity;

class AddressUser
{
    /** @var string|null */
    private $name;

    /** @var int */
    private $telephone;

    public function __construct(int $telephone, ?string $name)
    {
        $this->telephone = $telephone;
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getTelephone(): int
    {
        return $this->telephone;
    }

    public function setTelephone(int $telephone): self
    {
        $this->telephone = $telephone;
        return $this;
    }
}
