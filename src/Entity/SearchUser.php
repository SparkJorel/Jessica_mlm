<?php

namespace App\Entity;

class SearchUser
{
    private $fullname;

    private $city;

    /**
     * @return string
     */
    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    /**
     * @param string $fullname
     * @return SearchUser
     */
    public function setFullname(string $fullname): self
    {
        $this->fullname = $fullname;
        return $this;
    }

    /**
     * @return string
     */
    public function getCity(): ?string
    {
        return $this->city;
    }

    /**
     * @param string $city
     * @return SearchUser
     */
    public function setCity(string $city): self
    {
        $this->city = $city;
        return $this;
    }
}
