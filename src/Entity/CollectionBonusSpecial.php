<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CollectionBonusSpecial
{
    /**
     * @var Collection|BonusSpecial[]
     */
    private $bonusSpecials;

    public function __construct()
    {
        $this->bonusSpecials = new ArrayCollection();
    }

    /**
     * @return BonusSpecial[]|Collection
     */
    public function getBonusSpecials()
    {
        return $this->bonusSpecials;
    }

    public function addBonusSpecial(BonusSpecial $bonusSpecial)
    {
        if (!$this->bonusSpecials->contains($bonusSpecial)) {
            $this->bonusSpecials[] = $bonusSpecial;
        }

        return $this;
    }

    public function removeBonusSpecial(BonusSpecial $bonusSpecial)
    {
        if ($this->bonusSpecials->contains($bonusSpecial)) {
            $this->bonusSpecials->removeElement($bonusSpecial);
        }

        return $this;
    }
}
